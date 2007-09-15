<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2007 Maikel Linke, Christian Zedler
 */
 
 require_once 'files/class_new_files.php';
 require_once 'class_Path.php';

 class output {
  var $root;
  var $lang;
  var $tmpl;
  var $nav = array();
  var $title;
  var $headline;

  var $vars;
  var $output;
  var $content;
  var $out_of_service = false;

  function output() {
   $this->root = $GLOBALS['root'];
   $this->vars = array();
  }
  
  function set_tmpl() {
   $filename = 'output.html';
   if (session_is_registered('userid')) {
    global $db;
    $db->select('opt from person where id="'.$_SESSION['userid'].'"');
    if ($db->data[0]['opt']&1 == 1) $filename = 'output_classic.html';
   }
   $this->tmpl = new tmpl($filename,array(),$this->root);
  }
  
  function format() {
   $this->nav2vars();
   $this->css2vars();
   $this->title2vars();
   $this->headline2vars();
   $this->menu2vars();
   $this->javascript2vars();
   $this->date2vars();
   $this->usermenu2vars();
   $this->global2vars();
   $this->tmpl->input[0] = $this->vars;
   $this->set_lang_files();
   $this->tmpl->input2data();
   $this->output = $this->tmpl->fdata;
  }
  
  function send() {
   header('Content-Type: text/html;charset=utf-8');
   header('Expires: Sun, 15 Nov 1987 23:50:30 GMT');
   header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
   header('Cache-Control: no-cache, must-revalidate');
   header('Pragma: no-cache');
   echo $this->output;
  }
  
  function out($content='') {
   $this->set_tmpl();
   if ($content) $this->vars['content'] = $content;
   if ($this->out_of_service) {
    $this->vars['content'] = new tmpl('out_of_service.html',array(),$this->root);
   }
   $this->format();
   $this->send();
  }

  function secure($auth='user',$passwd=false) {
   $content = '{authentification failed}';
   if ($passwd && isset($_GET['passwd'])) {
    $random_passwd = '';
    include $this->root.'.htsecret/etc/random.php';
    if ($random_passwd == $_GET['passwd']) return true;
   }
   if (session_is_registered('userid')) {
    if ($auth == 'user') return true;
    if ($auth == 'admin' && is_admin()) return true;
   }
   else $content = tmpl_register_link();
   $this->out($content);
   exit;
  }

  function mask($string) {
   $string = '{'.$string.'}';
   return $string;
  }
  
  function set_lang_files() {
   $this->tmpl->set_lang($this->lang);
   $dir_num = sizeof(explode('/',$this->root));
   $r = './';
   for ($i=0;$i<$dir_num;$i++) {
    $file = Path::linkto($r,$this->root).'output.lang';
    $this->tmpl->add_lang_file($file);
    $r.= '../';
   }
  }
  
  function nav2vars() {
   $nav = $this->nav;
   $vnav = array();
   foreach($nav as $rel => $ref) {
    $title = $this->mask($rel);
    $vnav[] = array('nav_rel'=>$rel,'nav_ref'=>$ref,'nav_title'=>$title);
   }
   $this->vars['nav'] = $vnav;
  }
  
  function css2vars() {
   $dir_num = sizeof(explode('/',$this->root));
   $css = array();
   $r = '';
   for ($i=0;$i<$dir_num;$i++) {
    if (file_exists($r.'style.css')) {
     $css[] = array('css_root'=>$r);
    }
    $r = '../'.$r;
   }
   $this->vars['css'] = $css;
  }
  
  function title2vars() {
   $title = $this->title;
   $this->vars['title0'] = $this->mask($title[0]);
   $vtitle = array();
   for($i=1;$i<sizeof($title);$i++){
    $vtitle[] = array('title'=>$this->mask($title[$i]));
   }
   $this->vars['title'] = $vtitle;
  }
  
  function headline2vars() {
   $hl = $this->headline;
   $this->vars['headline0'] = $this->mask($hl[0]);
   $headline = array();
   for ($i=1;$i<sizeof($hl);$i++){
    $headline[] = array('hnum'=>$i,'headline'=>$this->mask($hl[$i]));
   }
   $this->vars['headline'] = $headline;
  }
  
  function menu2vars() {
   $this->vars['menu'] = $this->menuitem('',$this->menu);
  }
  
  function javascript2vars() {
   $dir = opendir($this->root.'js/');
   while($datei = readdir($dir)){
    if(substr($datei,-10) == '.select.js'){
     $js[] = $datei;
    }
   }
   closedir($dir);
   $anz = sizeof($js);
   if($anz>0){
    if($anz>1){
     srand((double)microtime()*1000000);
     $i = rand(0,($anz-1));
    }
    else{
     $i = 0;
    }
    $javascript = 'js/'.$js[$i];
   }
   else $javascript = '';
   $this->vars['javascript'] = $javascript;
   $this->vars['scr_wait'] = get_scr('scr_wait');
   $this->vars['scr_delay'] = get_scr('scr_delay');
  }
  
  function date2vars() {
   $this->vars['day'] = $this->mask(date('D'));
   $this->vars['time'] = date('H:i');
   $this->vars['date'] = tmpl_date_title(datetime());
  }

  function usermenu2vars() {
   if(session_is_registered('userid')){
    $this->usermenu2vars_loggedin();
   }
   else{
    $this->usermenu2vars_login();
   }
  }
  
  function usermenu2vars_login() {
   $first_name = '';
   $last_name = '';
   $passwd = '';
   $save = '';
   $autologin = '';
   $rm_login = array();
   if (isset($_COOKIE['first_name'])) $first_name = $_COOKIE['first_name'];
   if (isset($_COOKIE['last_name'])) $last_name = $_COOKIE['last_name'];
   if (isset($_COOKIE['passwd'])) {
    $passwd = $_COOKIE['passwd'];
    $save = ' checked';
    $rm_login[] = array();
   }
   if (isset($_POST['first_name'])) $first_name = $_POST['first_name'];
   if (isset($_POST['last_name'])) $last_name = $_POST['last_name'];
   if (isset($_POST['passwd'])) {
    $passwd = $_POST['passwd'];
    if (isset($_POST['save'])) $save = ' checked';
    else $save = '';
   }
   if (isset($_POST['origin'])) $origin = $_POST['origin'];
   else $origin = $_SERVER['PHP_SELF'];
   if (isset($_COOKIE['userid'])) $rm_login[] = array();
   $vars = array(
    'root' => $this->root,
    'first_name' => $first_name,
    'last_name' => $last_name,
    'passwd' => $passwd,
    'save' => $save,
    'origin' => $origin,
    'rm_login' => $rm_login,
   );
   $this->vars['usermenu'] = new tmpl('login.html',$vars,$this->root);
  }

  function usermenu2vars_loggedin() {
   // information about the user
   $v['username'] = $_SESSION['first_name'].' '.$_SESSION['last_name'];
   $v['last_login_date'] = local_date($_SESSION['last_login'],'ymdHi');
   
    /*
     * active_sessions() is not stable
     * and should be rewritten
   $online_users = active_sessions();
   $ou_number = count($online_users);
   if ($ou_number > 0) {
    $ousers['number'] = $ou_number;
    $ousers['user'] = array();
    if ($ou_number < 10) {
     $ousers['user'] = $online_users;
    }
    $loggedin[]['usermenu'] = new tmpl('loggedin_online_users.html',$ousers,$this->root);
   }
   */

   $pantrag = $gantrag = 0;
   if (is_admin()){
    $pantrag = sizeof(get_neu_account());
   }
   $gantrag = sizeof(get_neu_pg($_SESSION['userid']));
   if(($pantrag+$gantrag)>0){
    $antrag_person = array();
    $antrag_gruppe = array();
    if($pantrag) $antrag_person[0] = array('antrag_person_zahl'=>$pantrag);
    if($gantrag) $antrag_gruppe[0] = array('antrag_gruppe_zahl'=>$gantrag);
    $antrag['person'] = $antrag_person;
    $antrag['group'] = $antrag_gruppe;
    $loggedin[]['usermenu'] = new tmpl('loggedin_request.html',$antrag,$this->root);
   }
   $msg = get_msgs_number($_SESSION['userid']);
   $message_num = new_message_num();
   if($message_num>0){
    $loggedin[]['usermenu'] = new tmpl('loggedin_messages.html',array('message_num'=>$msg),$this->root);
   }
   
   $new_files = new new_files();
   $file_num = $new_files->number();
   if ($file_num > 0) {
   	$loggedin[]['usermenu'] = new tmpl('loggedin_files.html',array('file_num'=>$file_num),$this->root);
   }

   $entry_num = forum_new_entries();
   if($entry_num > 0){
    $loggedin[]['usermenu'] = new tmpl('loggedin_forum.html',array('entry_num'=>$entry_num),$this->root);
   }
   /* 
   $dzahl = get_neu_dateien_zahl();
   if($dzahl>0){
    $loggedin[]['usermenu'] = new tmpl('loggedin_file.html',array('file_num'=>$dzahl),$this->root);
   }
   */
   if (is_admin()) {
    $news_num = inactive_news_num();
    if ($news_num>0) {
     $loggedin[]['usermenu'] = new tmpl('loggedin_news.html',array('news_num'=>$news_num),$this->root);
    }
   } 

   $this->loggedin_sessinfo($loggedin,'error');
   $this->loggedin_sessinfo($loggedin,'notice');
   
   $v['usermenu'] = $loggedin;
   $this->vars['usermenu'] = new tmpl('loggedin.html',$v,$this->root);
  }

  function loggedin_sessinfo(&$v,$type) {
   if (isset($_SESSION[$type])) {
    $info = array();
    if (is_array($_SESSION[$type])) {
     $info = $_SESSION[$type];
    }
    if (is_string($_SESSION[$type])) {
     $info[] = $_SESSION[$type];
    }
    foreach ($info as $i => $text) {
     $text = $this->mask($text);
     $tmplname = 'loggedin_'.$type.'.html';
     $v[]['usermenu'] = new tmpl($tmplname,array('text'=>$text),$this->root);
    }
    $_SESSION[$type] = array();
   }
  }

  function global2vars() {
   $this->vars['root'] = $this->root;
   if(isset($_POST['origin'])) $origin = $_POST['origin'];
   else $origin = $_SERVER['PHP_SELF'];
   $this->vars['origin'] = $origin;
   $loadtime = millitimestamp() - millitimestamp($GLOBALS['microtime0']);
   $this->vars['loadtime'] = round($loadtime,1);
  }
  
  function format_menuitem($url,$caption,$level,$dir=false) {
   $url = Path::linkto($GLOBALS['root'].$url);
   $caption = output::mask($caption);
   $m = array(
    'menu_spacer' => array(),
    'menu_entry' => array(),
    'menu_dir' => array(),
    'menu_url' => $url,
    'menu_caption' => array(),
    'menu_caption_this' => array(),
   );
   for($i=0;$i<$level;$i++) {
    $m['menu_spacer'][] = array();
   }
   if($dir) {
    $m['menu_dir'][] = array();
   }
   else {
    $m['menu_entry'][] = array();
   }
   $document_root = $_SERVER['DOCUMENT_ROOT'];
   if (substr($document_root,-1) == '/') $document_root = substr($document_root,0,-1);
   $a = Path::absolute($url);
   $b = sessionurl($a);
   $c = $document_root.$_SERVER['REQUEST_URI'];
   if($a==$c || $b==$c) $m['menu_caption_this'][0]['menu_caption'] = $caption;
   else $m['menu_caption'][0]['menu_caption'] = $caption;
   return $m;
  }
  
  function menuitem($url,$caption,$level=0) {
   $item = array();
   if(is_array($caption)){
    if($url){
     $item[] = $this->format_menuitem($url,$caption['./'],$level,true);
     unset($caption['./']);
    }
    foreach($caption as $u => $c){
     $item = array_merge($item,$this->menuitem($url.$u,$c,$level+1));
    }
   }
   else{
    $item[] = $this->format_menuitem($url,$caption,$level);
   }
   return $item;
  }
 }

?>