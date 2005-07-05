<?php
 include 'var.php';

 if($_POST['nachname']){
  if(is_array($person = get_person(0,$_POST['vorname'],$_POST['nachname'],0))){
   srand((double)microtime()*1000000);
   $passwd = rand(1000,9999);
   get_mc();
   mysql_query('update person set passwd=password("'.$passwd.'") where id="'.$person[id].'"');
   mysql_close();
   $text = 'Ihnen wurde ein neues Passwort generiert.'."\n";
   $text.= 'Es lautet: '.$passwd."\n";
   $text.= 'Sie können es in Ihrem Benutzerprofil wieder ändern.';
   $mailbody = get_mailbody(html2text($person[name]),$text);
   $sent = mail($person[mail],'[Infoschool] Neues Passwort',$mailbody,$GLOBALS['mailheader']);
   if($sent){
    $inhalt = '<p>Es wurde ein neues Passwort generiert und per Mail verschickt.</p>';  // todo: umleitung auf neues seite
   }
   else{
    $inhalt = '<p>Es konnte keine Mail verschickt werden: '.$person[mail].'</p>';
   }
  }
  else{
   $inhalt = '<p>Entweder gibt es keinen Account unter diesem Namen oder es gibt gleich mehrere. Bitte informiere den Administrator.</p>';
  }
 }

 if(!($vor=$_POST['vorname']))$vor=$_GET['vorname'];
 if(!($nach=$_POST['nachname']))$nach=$_GET['nachname'];
 $v['%vorname%'] = $vor;
 $v['%nachname%'] = $nach;
 $inhalt.= get_face('neu_passwd.html',$v);
 $html_titel[] = 'Neues Passwort';
 $html_headline[] = 'Neues Passwort';

 echo face($inhalt);
?>