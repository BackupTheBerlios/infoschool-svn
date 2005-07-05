<?php
 include 'var.php';

 // fügt eine ID-Liste der Adminliste hinzu
 function neu($typ,$ids){
  if(sizeof($ids)>0){
   if($typ=='gruppe'){
    foreach($ids as $gid => $gname){
     $mitglied = get_personen($gid);
     foreach($mitglied as $i => $p){
      $pids[$p['id']] = '1';
     }
    }
   }
   else{
    $pids = $ids;
   }
   $mc = get_mc();
   foreach($pids as $pid => $pname){
    mysql_query('insert into admin values("'.$pid.'")');
   }
   mysql_close($mc);
  }
  redirect('./');
 }

 list($status,$typ,$ids) = manage_ids();
 if($status==1){
  if(sizeof($ids)>0){
   if(sizeof($ids)==1) $status++;
   else $inhalt = get_form(ask_ids($typ,$ids),'Hinzuf&uuml;gen');
  }
  else{
   $_SESSION['meldungen'][] = 'Name nicht gefunden.';
   $status--;
  }
 }
 if($status==0) $inhalt = ask_name();
 if($status==2) neu($typ,$ids);
 $html_titel[] = 'Personen der Adminliste hinzuf&uuml;gen';
 $html_headline[] = 'Personen der Adminliste hinzuf&uuml;gen';
 echo face($inhalt);
?>