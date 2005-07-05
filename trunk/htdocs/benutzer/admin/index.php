<?php
 include 'var.php';
 $html_titel[] = 'Admin';
 $html_headline[] = 'Adminliste';

 function get_adminliste(){
  $mc = get_mc();
  $admins = mysql_query('select person.id,person.first_name as vorname,person.last_name as nachname from admin,person where admin.pid=person.id order by person.last_name,person.first_name');
  $liste = '<ul>';
  while(list($id,$vorname,$nachname) = mysql_fetch_row($admins)){
   $liste.= '<li><a href="../view_person.php?pid='.$id.'">'.$nachname.', '.$vorname.'</a> - <a href="rm.php?pid='.$id.'&passwd='.$_GET['passwd'].'">'.htmlformat_symbol('rm','entfernen').'</a></li>';
  }
  $liste.= '</ul>';
  mysql_close($mc);
  return $liste;
 }

 $inhalt = get_adminliste();
 $inhalt.= ask_name();
 echo face($inhalt);
?>