<?php
 include 'var.php';

 // trägt neue Messages in die Datenbank ein
 // und verschickt sie, wenn erwünscht, per Mail
 function neu_msg($typ,$ids,$text,$aid,$autor){
  if($typ=='gruppe'){
   $query = 'select person.id,person.first_name as vorname,person.last_name as nachname,person.mail,person.opt from person,pg where person.id=pg.pid and (';
   $or = '';
   foreach($ids as $id => $name){
    $query.= $or.'pg.gid="'.$id.'"';
    $or = ' or ';
   }
   $query.= ')';
  }
  else{
   $query = 'select id,first_name as vorname,last_name as nachname,mail,opt from person where ';
   $or = '';
   foreach($ids as $id => $name){
    $query.= $or.'id="'.$id.'"';
    $or = ' or ';
   }
  }
  $mc = get_mc();
  $ownerrows = mysql_query($query);
  while($o = mysql_fetch_array($ownerrows)){
   $o['name'] = $o['vorname'].' '.$o['nachname'];
   $o['opt'] = decbin($o['opt']);
   while(!isset($o['opt'][7]))$o['opt']='0'.$o['opt'];
   if($o['opt'][3]){
    $status = 3;
    $mailbody = get_mailbody(html2text($o['name']),'Im Infoschool-System hat dir jemand folgende Message geschickt:'."\n\n".html2text($text));
    @mail($o['mail'],'[Infoschool] Neue Message von '.$autor,$mailbody,$GLOBALS['mailheader']);
   }
   else{
    $status = 7;
   }
   mysql_query('insert into msg values("","'.$o['id'].'","'.$o['name'].'","'.$aid.'","'.$autor.'",now(),"'.format_msg($text).'","'.$status.'")');
  }
  mysql_close($mc);
  redirect('./');
 }

 // gibt den Text einer Message zurück
 function get_message_text($mid){
  get_mc();
  $msg = mysql_query('select text from msg where id="'.$mid.'" and oid="'.$_SESSION['userid'].'"');
  mysql_close();
  list($text) = mysql_fetch_row($msg);
  if($text)
   $text = '[quote]'.html2textarea($text).'[/quote]'."\n";
  return $text;
 }

 // erstellt ein Formular für eine neue Message oder verschickt eine fertige
 function manage_ids_msg($typ,$ids){
  $text = '';
  $ch = '';
  $inhalt = '<input type="hidden" name="typ" value="'.$typ.'" />';
  if(isset($_POST['text'])){
   $text = $_POST['text'];
   if(isset($_POST['pre'])){
    $mvar['%z%'] = 2;
    $mvar['%text%'] = stripslashes(format_msg($_POST['text']));
    $mvar['%person%'] = $_SESSION['name'];
    $mvar['%datum%'] = htmlformat_datum(date('Y-m-d H:i:s'));
    $mvar['%options%'] = '';
    $inhalt.= get_face('msg.html',$mvar);
    $ch = ' checked';
   }
   else{
    neu_msg($typ,$ids,$text,$_SESSION['userid'],$_SESSION['name']);
   }
  }
  else{
   if(isset($_GET['mid'])){
    $text = get_message_text($_GET['mid']);
   }
  }
  $inhalt.= '<p><b>Empf&auml;nger:</b> ';
  $zwz = '';
  foreach($ids as $id => $name){
   $inhalt.= '<input type="hidden" name="ids['.$id.']" value="'.$name.'" />';
   $inhalt.= $zwz.$name;
   $zwz = ', ';
  }
  $inhalt.= '</p>';
  $inhalt.= htmlformat_textarea($text);
  $inhalt.= '<input type="checkbox" name="pre" value="1"'.$ch.' /> Vorschau';
  return get_form($inhalt,'Weiter');
 }

 if(session_is_registered('userid')){
  list($status,$typ,$ids) = manage_ids();
  if($status == 1){
   if(sizeof($ids)>0){
    if(sizeof($ids)==1) $status++;
    else $inhalt = get_form(ask_ids($typ,$ids),'Weiter');
   }
   else{
    $_SESSION['meldungen'][] = 'Name nicht gefunden';
    $status--;
   }
  }
  if($status == 0) $inhalt = ask_name('Empf&auml;nger');
  if($status == 2) $inhalt = manage_ids_msg($typ,$ids);
  $html_titel[] = 'Neue Message';
  $html_headline[] = 'Neue Message';
 }else $inhalt = get_anmeldung_link();

 echo face($inhalt);
?>