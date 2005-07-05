<?php
 include 'var.php';

 // entfernt eine Person samt aller dazugehörigen Datenbankeinträge
 function rm_person($pid){
  $mc = get_mc();
  $foren = mysql_query('select id from forum where owner="'.$pid.'"');
  while($f = mysql_fetch_row($foren)){
   $threads = mysql_query('select id from forum_thread where forum="'.$f[0].'"');
   while($t = mysql_fetch_row($threads)){
    mysql_query('delete from forum_post where thread="'.$t[0].'"');
   }
   mysql_query('delete from forum_thread where forum="'.$f[0].'"');
   mysql_query('delete from forum_recht_p where forum="'.$f[0].'"');
   mysql_query('delete from forum_recht_g where forum="'.$f[0].'"');
  }
  mysql_query('delete from forum where owner="'.$pid.'"');
  mysql_query('delete from forum_recht_p where pid="'.$pid.'"');
  $gruppen = mysql_query('select id from gruppe where leiter="'.$pid.'"');
  while($g = mysql_fetch_row($gruppen)){
   mysql_query('delete from pg where gid="'.$g[0].'"');
   mysql_query('delete from forum_recht_g where gid="'.$g[0].'"');
   $stunden = mysql_query('select id from lesson where gid="'.$g[0].'"');
   while($s = mysql_fetch_row($stunden)){
    mysql_query('delete from vertretung where sid="'.$s[0].'"');
   }
   mysql_query('delete from lesson where gid="'.$g[0].'"');
  }
  mysql_query('delete from gruppe where leiter="'.$pid.'"');
  mysql_query('delete from pg where pid="'.$pid.'"');
  mysql_query('delete from neu_pg where pid="'.$pid.'"');
  mysql_query('delete from todo where pid="'.$pid.'"');
  mysql_query('delete from zensuren where pid="'.$pid.'"');
  mysql_query('delete from admin where pid="'.$pid.'"');
  mysql_query('delete from person where id="'.$pid.'"');
  mysql_query('update forum_post set aid=null where aid="'.$pid.'"');
  mysql_query('update msg set status=status&6 where aid='.$pid);
  mysql_query('update msg set status=status&5 where oid='.$pid);
  mysql_query('delete from msg where status|4=4 and (aid='.$pid.' or oid='.$pid.')');
  mysql_close($mc);

  //Variabel $datei dient nur der Vereinfachung
  $datei = $GLOBALS[img_person_dir][intern].$pid;

  //Löschen evtl. vorhandener alter Dateien (mit anderer Endung
  if (file_exists($datei.'.jpg')) unlink($datei.'.jpg');
  if (file_exists($datei.'.gif')) unlink($datei.'.gif');
 }

 if(session_is_registered('userid')){
  $pid = $_SESSION['userid'];
  if($_SESSION['admin']){
   if($_POST['pid']) $pid = $_POST['pid'];
  }
  if($_POST['sicher']){
   rm_person($pid);
   if($pid==$_SESSION['userid']){
    session_destroy();
    redirect('rm_person_done.php');
   }
   else{
    redirect('./');
   }
  }
  else{
   if(is_array($person=get_person($pid))){
    if($person[id] == $_SESSION['userid']){
     $vars['%account%'] = 'deinen Account';
    }
    else{
     $vars['%account%'] = 'den Account von '.$person[vorname].' '.$person[nachname];
     $input = '<input type="hidden" name="pid" value="'.$person[id].'"></input>';
    }
    $vars['%warnung%'] = '';
    $mc = get_mc();
     $gruppen = mysql_query('select count(id) from gruppe where leiter="'.$person[id].'"');
     $stunden = mysql_query('select count(stunde.id) from gruppe,lesson where leiter="'.$person[id].'" and gruppe.id=stunde.gid');
     $foren = mysql_query('select count(forum.id) from forum where owner="'.$person[id].'"');
    mysql_close();
    list($gz) = mysql_fetch_row($gruppen);
    list($sz) = mysql_fetch_row($stunden);
    list($fz) = mysql_fetch_row($foren);
    if(($gz+$sz+fz)>0){
     $vars['%warnung%'] = 'Mit diesem Account werden auch alle dazu geh&ouml;renden Gruppen ('.$gz.'), Stunden ('.$sz.'), Foren ('.$fz.') etc. gel&ouml;scht. Diese sollten vorher an andere Personen &uuml;bergeben werden.';
    }
    $input.= '<input type="hidden" name="sicher" value="1"></input>';
    $vars['%form%'] = get_form($input,'Ja, l&ouml;schen!');
    $inhalt = get_face('rm_person.html',$vars);
   }
  }
 }

 echo face($inhalt);
?>