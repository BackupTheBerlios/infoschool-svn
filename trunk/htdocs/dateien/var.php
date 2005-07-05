<?php
/*
Rechtedefinition:
Lesen -> 1
Schreiben -> 2
Eigenes Editieren -> 4
Eigenes Löschen -> 8
Editieren -> 16
Löschen -> 32
Rechte ändern -> 64
*/
 include 'func.php';
 if(!isset($root)) $root = '';
 $root.= '../';
 include $root.'var.php';
  $html_menu = add_subdir($html_menu);
 $html_titel[] = 'Dateiverwaltung';
  $html_menu['Dateiaustausch'] = array(
  '0' => './',
  'Rechte' => 'rechte.php',
  'Verwalten' => 'admin.php',
 );
// $rfs = add_subdir($rfs);
 
 $upload_dir = $root.'../upload/';

$maximale_dateigroesse = "5000000";

function ausgeben_alt($formular, $replacements = array()) // kann geloescht werden
{	
//fuer jeden Array-Wert die passenden Wert loeschen
foreach($replacements as $key => $value) $formular = str_replace("%$key%", $value, $formular);
//ersetzt nicht angegebene Strings mit Leerstrings (z.B.: "%id%" mit "")
$formular = preg_replace("/%[^ <>]+%/", "", $formular);
//Text ausgeben
echo $formular;
}	

function ausgeben($formular, $replacements = array())
{	
//fuer jeden Array-Wert die passenden Wert loeschen
foreach($replacements as $key => $value) $formular = str_replace("%$key%", $value, $formular);
//ersetzt nicht angegebene Strings mit Leerstrings (z.B.: "%id%" mit "")
$formular = preg_replace("/%[^ <>]+%/", "", $formular);
return $formular;
}	

?>
