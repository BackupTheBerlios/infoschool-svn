<?php
 include 'var.php';

 $html_titel[] = 'Personen';
 $html_headline[] = 'Benutzer';
 if(session_is_registered('userid')){
  $inhalt = htmlformat_menu_abc();
  $abc = 'A';
  if(isset($_SESSION['person_abc'])) $abc = $_SESSION['person_abc'];
  if(isset($_GET['abc'])){
   $abc = $_GET['abc'];
   $_SESSION['person_abc'] = $abc;
  }
  $person = get_personen(0,$abc);
  $n = sizeof($person);
  if($n>0){
   $inhalt.= get_personen_link($person);
   $inhalt.= '<p align="center">Summe: '.$n.'</p>';
  }
  else{
   $inhalt.= '<p>In dieser Rubrik gibt es noch keine Benutzer.</p>';
  }
 }
 else{
  $inhalt = get_anmeldung_link();
 }

 echo face($inhalt);
?>