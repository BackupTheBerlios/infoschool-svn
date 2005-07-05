<?php
 include 'var.php';

function avatar_loeschen()
{
  //Variabel $datei dient nur der Vereinfachung
  $datei = $GLOBALS[img_person_dir][intern].$_SESSION[userid];
  
  //Löschen evtl. vorhandener alter Dateien (mit anderer Endung
  if (file_exists($datei.'.jpg')) unlink($datei.'.jpg');
  if (file_exists($datei.'.gif')) unlink($datei.'.gif');

  $_SESSION['meldungen'][] = 'Avatar gel&ouml;scht!';
  redirect('view_person.php');
}

 function get_remote_file($url)
 {
  if (substr($url, 0,7) != "http://") $url = "http://".$url;
  
  $file = @fopen ($url, "rb");
  if ($file == false) {
  $_SESSION['meldungen'][] = 'Remote Datei konnte nicht ge&ouml;ffnet werden!';
  redirect('view_person.php');
  }
  while (!feof($file)) $inhalt.=fread($file,1024);
  fclose ($file);
  
  $filename = tempnam ($GLOBALS[img_person_dir][intern], "tmp_");

  $file = fopen($filename, "wb");
  fwrite($file, $inhalt);
  fclose($file);
  
  return $filename;
 }
 
function mod_avatar($file){

$pic_info = GetImageSize($file);
  
  //Zu große Dateien herausfiltern
  if (filesize($file) > $GLOBALS["max_avatar_groesse"])
 	{
     $_SESSION['meldungen'][] = 'Fehler beim Upload: Die Datei ist zu gro&szlig;!';
     if (file_exists($file)) unlink($file);
	 redirect('view_person.php');
	}
	
  //Fehlerhafte Uploads herausfiltern
  if (filesize($file) == 0 OR filesize($file) < 10)
 	{
     $_SESSION['meldungen'][] = 'Unbekannter Fehler beim Upload!';
	 if (file_exists($file)) unlink($file);
	 redirect('view_person.php');
	}
  
  //Unerlaubte Dateitypen herausfiltern + Dateiendung bestimmen
  if ($pic_info[2] == 1) {$endung = '.gif';}
  elseif ($pic_info[2] == 2) {$endung = '.jpg';}
  else
 	{
     $_SESSION['meldungen'][] = 'Fehler beim Upload: Es werden nur Bilder (GIF, JPG) akzeptiert!'; 
	 if (file_exists($file)) unlink($file);
	 redirect('view_person.php');
	}
  
  //Abmessungen der Datei bestimmen
  if (($pic_info[0] > 300) OR ($pic_info[1] > 300))
 	{
     $_SESSION['meldungen'][] = 'Fehler beim Upload: Das Bild ist zu gro&szlig;!'; 
     if (file_exists($file)) unlink($file);
	 redirect('view_person.php');
	}
	
  //Variabel $datei dient nur der Vereinfachung
  $datei = $GLOBALS[img_person_dir][intern].$_SESSION[userid];
  
  //Löschen evtl. vorhandener alter Dateien (mit anderer Endung
  if (file_exists($datei.'.jpg')) unlink($datei.'.jpg');
  if (file_exists($datei.'.gif')) unlink($datei.'.gif');
  
  //Datei verschieben
  copy($file, $datei.$endung);
  if (file_exists($file)) unlink($file);
  
  //Änderungen in der Datenbank einfügen
  $mc = get_mc();
  mysql_query('update person set opt=(opt | 8) where id="'.$_SESSION['userid'].'"');
  mysql_close($mc);
 }

if(session_is_registered('userid')){
  if (isset($remote_file)) $file = get_remote_file($remote_file);
  if (isset($loeschen)) avatar_loeschen();
  else  mod_avatar($file);
  redirect('view_person.php');
 }
 else $inhalt = get_anmeldung_link();

 echo face($inhalt);
?>