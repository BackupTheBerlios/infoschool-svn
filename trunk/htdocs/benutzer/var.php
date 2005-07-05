<?php
 include 'func.php';
 if(!isset($root)) $root = '';
 $root.= '../';
 include $root.'var.php';
 $html_menu = add_subdir($html_menu);
 $html_menu['Benutzer'] = array(
  '0' => './',
  'Gruppen' => 'gruppen.php',
  'Gruppe Verwalten' => 'mod_gruppe.php',
 );
 if(isset($_SESSION['admin']) && $_SESSION['admin']){
  $html_menu['Benutzer']['Admin'] = 'admin/?passwd=sto73a'; // Passwort fuer Benutzeradmin hier eintragen
 }
 $html_titel[] = 'Benutzerverwaltung';
/*
 Jede Person hat 8 Optionen (1 Byte), die folgendes bewirken:
 1 Geburtsdatum	  ver�ffentlichen
 2 E-Mailadresse	  ver�ffentlichen
 3 ICQ-UIN	  ver�ffentlichen - f�llt mitlerweile weg
 4 fr�her Bild ver�ffentlichen, mitlerweile nicht belegt
 5 Messages 	  per Mail schicken
 6 Vertretungsplan per Mail schicken
 7 noch frei
 8 noch frei
 Ist das Byte in bin�rer Darstellung als String gespeichert, spricht man die
 Optionen von hinten nach vorne sortiert an: $string[8-$optionsnummer].
 Option 1 ist dann also $string[7].
*/
?>
