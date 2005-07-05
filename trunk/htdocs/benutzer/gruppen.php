<?php
 include 'var.php';
 $html_titel[] = 'Gruppen';
 $html_headline[] = 'Gruppen';

 if(session_is_registered('userid')){
  $inhalt = htmlformat_menu_abc('gruppen.php');
  if(isset($_GET['abc']) && ($abc = $_GET['abc'])!='') $_SESSION['gruppe_abc'] = $abc;
  else $abc = $_SESSION['gruppe_abc'];
  if($abc=='') $abc = 'A';
  $gruppen = get_gruppen(0,0,$abc);
  $n = sizeof($gruppen);
  if($n>0){
   $inhalt.= get_gruppen_link($gruppen);
   $inhalt.= '<p align="center">Summe: '.$n.'</p>';
  }
  else{
   $inhalt.= '<p>In dieser Rubrik gibt es noch keine Gruppen.</p>';
  }
 }else $inhalt = get_anmeldung_link();

 echo face($inhalt);
?>