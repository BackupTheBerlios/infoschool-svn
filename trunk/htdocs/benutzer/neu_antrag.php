<?php
 include 'var.php';

 // erstellt einen Antrag für einen neuen Account und gibt Fehlermeldungen zurück
 function neu_account($person){
  if(!$person['accept'])
   $error['accept'] = 'Ohne Zustimmung zu den Benutzungsbedingungen darf kein Account erstellt werden.';
  if(!checkdate($person['gebmon'],$person['gebtag'],$person['gebjahr']))
   $error['gebdat'] = 'Das Geburtsdatum ist ung&uuml;ltig.';
  if(!($person['passwd1']==$person['passwd2']))
   $error['passwd'] = 'Die beiden Passw&ouml;rter stimmten nicht &uuml;berein.';
  if(sizeof($error)==0){
   $person['vorname'] = text2html($person['vorname']);
   $person['nachname'] = text2html($person['nachname']);
   $person['gebdat'] = $person['gebjahr'].'-'.$person['gebmon'].'-'.$person['gebtag'];
   $mc = get_mc();
   mysql_query('insert into neu_account values("",password("'.text2html($person['passwd1']).'"),"'.$person['vorname'].'","'.$person['nachname'].'","'.$person['gebdat'].'","'.$person['mail'].'")');
   mysql_close($mc);
   redirect('neu_antrag.php?neu=1');
  }
  return $error;
 }

 $person = array();
 $inhalt = '';
 $error = array();
 if(isset($_POST['person'])) $person = $_POST['person'];
 if(sizeof($person)>0){
  $error = neu_account($person);
 }
 if(isset($_GET['neu']))
  $inhalt.= '<p><font color=#008800>Der Antrag wurde erfolgreich gestellt. Wenn er genehmigt wurde, wird eine Nachricht an die angegebene E-Mailadresse geschickt. Das kann allerdings etwas dauern.</font></p>';
 if(sizeof($error)>0){
  $inhalt.= '<font color=#ff0000><p>Der Antrag wurde aus folgenden Gr&uuml;nden nicht erstellt:</p>';
  foreach($error as $i => $v){
   $inhalt.= '<p>'.$v.'</p>';
  }
  $inhalt.= '</font>';
 }
 $input = get_neu_person_form($person);
 $inhalt.= get_form($input,'Beantragen');
 $html_titel[] = 'Neuer Account';
 $html_headline[] = 'Neuen Account beantragen';
 echo face($inhalt);
?>