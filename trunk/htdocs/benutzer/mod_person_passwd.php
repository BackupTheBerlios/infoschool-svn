<?php
 include 'var.php';

 // ändert das Benutzerpasswort
 function mod_person($person){
 
  if($person['passwd1']==$person['passwd2'])
  {
  $q = 'update person set passwd=password("'.$person['passwd1'].'") where passwd=password("'.$person['passwd'].'") and id="'.$person['id'].'"';
  $mc = get_mc();
  mysql_query($q);
  if(mysql_affected_rows()) $_SESSION['meldungen'][] = 'Passwort wurde ge&auml;ndert.';
  else $_SESSION['error'][] = 'Das Passwort konnte nicht ge&auml;ndert werden!';
  mysql_close($mc);
  }
  else 
  {
  $_SESSION['error'][] = 'Geben Sie bitte zweimal das selbe Passwort ein!'; 
  }
  redirect('view_person.php');
 }

 if(session_is_registered('userid')){
  if(is_array($person=$_POST['person'])){
   if($_SESSION['userid']==$person['id']){
    mod_person($person);
   }
   else{
    redirect('view_person.php?pid='.$_SESSION['userid']);
   }
  }
  else{
   redirect('view_person.php');
  }
 }
 else $inhalt = get_anmeldung_link();

 echo face($inhalt);
?>