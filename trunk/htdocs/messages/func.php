<?php

 // gibt Messages einer Person wieder; die man bekommen hat, die man versendet hat, neue Messages
 function get_msgs($pid,$s=6,$t=0,$n=0){
  $msg = array();
  $q = 'select msg.id,msg.autor,msg.owner,msg.datum,msg.text,msg.status,msg.aid,msg.oid from msg';
  $q.= ' where msg.status&'.$s.'='.$s.' and (';
  if($s&1){
   $q.= 'msg.aid="'.$pid.'"';
   if($s&2) $q.= ' or msg.oid="'.$pid.'"';
  }
  else $q.= 'msg.oid="'.$pid.'"';
  $q.= ')';
  if((int)$t) $q.= ' and datum>"'.date('Y-m-d',strtotime('-'.$t.' days')).'"';
  $q.= ' order by msg.datum DESC';
  if($n) $q.= ' limit '.$n;
  $mc = get_mc();
  $msgs = mysql_query($q);
  mysql_close($mc);
  while($m = mysql_fetch_array($msgs)){
   $msg[$m['id']] = $m;
  }
  return $msg;
 }

 // gibt eine Message in einer Tabelle formatiert zurück
 function htmlformat_msg($msg){
  $options = '';
  $userid = $_SESSION['userid'];
  if($msg['oid']==$userid){
   $prefix = 'Von: ';
   $person = $msg['autor'];
   $pid = $msg['aid'];
   if($msg['aid']==$userid) $srm = 4;
   else $srm = 5;
   if($msg['status']&4){
    $options.= '<a href="mod.php?mid='.$msg['id'].'&modus=%26&status=3">'.htmlformat_symbol('r','gelesen').'</a><tt>&#160;</tt>';
   }
   else{
    $options.= '<a href="mod.php?mid='.$msg['id'].'&modus=%7C&status=4">'.htmlformat_symbol('ur','ungelesen','.png').'</a><tt>&#160;</tt>';
   }
  }
  else{
   $prefix = 'An: ';
   $person = $msg['owner'];
   $pid = $msg['oid'];
   $srm = 6;
  }
  $options.= '<a href="mod.php?mid='.$msg['id'].'&modus=%26&status='.$srm.'">'.htmlformat_symbol('rm','l&ouml;schen').'</a><tt>&#160;</tt>';
  if($pid){
   $person = '<a href="../users/person.php?id='.$pid.'">'.$person.'</a>';
   $options.= '<a href="neu.php?typ=person&id='.$pid.'&mid='.$msg['id'].'">'.htmlformat_symbol('mail','antworten').'</a><tt>&#160;</tt>';
  }
  $v['%text%'] = $msg['text'];
  $v['%person%'] = $prefix.$person;
  $v['%datum%'] = htmlformat_datum($msg['datum']);
  $v['%options%'] = $options;
  return get_face('msg.html',$v);
 }

 // gibt Messages formatiert zurück
 function htmlformat_msgs($msg){
  $msgs = '';
  if(sizeof($msg)>0){
   foreach($msg as $mid => $m){
    $msgs.= htmlformat_msg($m);
   }
  }
  else{
   $msgs = '<p><b>Keine Messages.</b></p>';
  }
  return $msgs;
 }

 // löscht eine Message-ID aus der neu-Tabelle
 function rm_msg_neu($mid,$pid){
  $mc = get_mc();
  mysql_query('update msg set status=status&6 where id="'.$mid.'" and oid="'.$pid.'"');
  $idrow = mysql_query('select id from msg where id="'.$mid.'" and oid="'.$pid.'"');
  while(list($id) = mysql_fetch_row($idrow)){
   mysql_query('delete from msg_neu where mid="'.$id.'"');
  }
  mysql_close($mc);
  redirect('./?n=1');
 }

?>