<?php
 include 'var.php';

 // löscht eine Gruppe samt Stunden, Vertretungen und Rechten
 function rm_gruppe($gid){
  $mc = get_mc();
  $stunden = mysql_query('select id from stunde where gid="'.$gid.'"');
  while(list($sid) = mysql_fetch_row($stunden)){
   mysql_query('delete form vertretung where sid="'.$sid.'"');
  }
  mysql_query('delete from stunde where gid="'.$gid.'"');
  mysql_query('delete from forum_recht_g where gid="'.$gid.'"');
  mysql_query('delete from neu_pg where gid="'.$gid.'"');
  mysql_query('delete from pg where gid="'.$gid.'"');
  mysql_query('delete from gruppe where id="'.$gid.'"');
  mysql_close($mc);
  redirect('mod_gruppe.php');
 }

 if(session_is_registered('userid')){
  if(is_array($gruppe=get_gruppe($_POST['gid']))){
   if($gruppe[lid]==$_SESSION['userid']){
    if($_POST['sicher']){
     rm_gruppe($gruppe[id]);
    }
    else{
     $inhalt = '<p>Soll die Gruppe wirklich mit allen dazugeh&ouml;renden Stunden und Vertretungen gel&ouml;scht werden?</p>';
     $input = '<input type="hidden" name="gid" value="'.$gruppe[id].'"></input>';
     $input.= '<input type="hidden" name="sicher" value="1"></input>';
     $inhalt.= get_form($input,'Ja, l&ouml;schen!');
    }
   }
   else{
    redirect('mod_gruppe.php');
   }
  }
  else{
   redirect('mod_gruppe.php');
  }
 }
 else{
  $inhalt = get_anmeldung_link();
 }

 echo face($inhalt);
?>