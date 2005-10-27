<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2005 Maikel Linke
 */
 
 class news {
  var $data;
  
  function news($data=array()) {
   $this->data = $data;
  }
  
  function format($show_menu=false) {
   $data = $this->data;
   $data['start_html'] = local_datetime_title($data['start']);
   $data['end_html'] = local_datetime_title($data['end']);
   $data['text_html'] = html_br($data['text']);
   $data['menu'] = array(); 
   if ($show_menu && is_admin()) {
    if ($data['active']) $activate = 'Deactivate';
    else $activate = 'Activate';
    $menu['activate'] = $activate;
    $data['menu'][] = $menu; 
   }
   $data['input_start'] = datetime_input('news_data[start]',$data['start']);
   $data['input_end'] = datetime_input('news_data[end]',$data['end']);
   $this->data = $data;
  }
  
  function tmpl($tmpl='news.html') {
   $tmpl = new tmpl($tmpl,$this->data,$GLOBALS['root'].'news/');
   return $tmpl->fdata;
  }
  
  function load($id) {
   global $db;
   $query = 'news.id, 
             news.active, 
             news.start, 
             news.end,
             news.author, 
             person.first_name as author_first_name,
             person.last_name as author_last_name, 
             news.topic, 
             news.text 
            from news
            join person on
             news.author=person.id 
            where
             news.id="'.$id.'"'; 
   $db->select($query);
   $this->data = $db->data[0];
  }
  
  function save($data) {
   global $db;
   $start = implode_datetime($data['start']);
   $end = implode_datetime($data['end']);
   $query = 'news set
              start="'.$start.'",
              end="'.$end.'",
              topic="'.$data['topic'].'",
              text="'.$data['text'].'"
             where
              id="'.$data['id'].'"';
   $db->update($query);
  }
  
  function create() {
   $this->data = array(
    'id' => '',
    'active' => '0',
    'start' => datetime(),
    'end' => datetime(strtotime('+1 week')),
    'author' => $_SESSION['userid'],
    'author_first_name' => $_SESSION['first_name'],
    'author_last_name' => $_SESSION['last_name'],
    'topic' => '',
    'text' => '',
   );
  }
  
  function insert($data) {
   global $db;
   $start = implode_datetime($data['start']);
   $end = implode_datetime($data['end']);
   $query = 'news (start,end,author,topic,text) values (
   	      "'.$start.'",
   	      "'.$end.'",
   	      "'.$_SESSION['userid'].'",
   	      "'.$data['topic'].'",
   	      "'.$data['text'].'"';
   $db->insert($query);
   if (!$db->error) $_SESSION['notice'][] = '{news inserted}';
  }
 }
 
?>