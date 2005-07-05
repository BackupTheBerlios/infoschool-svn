<?php
 include 'var.php';

 // fügt der Datenbank eine neue Person hinzu
 function neu_person($person){
  $mc = get_mc();
  $query = 'insert into person (passwd, first_name, last_name, birthday, mail)';
  $query.= ' values(password("'.$person['passwd1'].'"),"'.text2html($person['vorname']).'","'.text2html($person['nachname']).'","'.$person['gebdat'].'","'.$person['mail'].'")';
  mysql_query($query);
  $pid = mysql_insert_id($mc);
  mysql_query('insert into pg values("'.$pid.'","1")');
  mysql_close($mc);
  redirect('./');
 }

 $person = array();
 $inhalt = '';
 if(isset($_POST['person'])){
  $person = $_POST['person'];
  if(!$person['accept'])
   $error[] = 'Ohne Zustimmung zu den Benutzungsbedingungen darf kein Account erstellt werden.';
  if($person['passwd1']!=$person['passwd2'])
   $error[] = 'Die beiden Passw&ouml;rter stimmten nicht &uuml;berein.';
  if(sizeof($error)<1){
   neu_person($person);
  }
 }
 if(isset($error) && sizeof($error)>0){
  $inhalt.= '<font color=#ff0000>';
  foreach($error as $i => $v){
   $inhalt.= '<p>'.$v.'</p>';
  }
  $inhalt.= '</font>';
 }
 $input = get_neu_person_form($person);
 $inhalt.= get_form($input,'Hinzuf&uuml;gen','neu_person.php?passwd='.$_GET['passwd']);
 $html_titel[] = 'Neuer Benutzer';
 $html_headline[] = 'Neuer Benutzer';
 echo face($inhalt);
?>