<?php
include 'var.php';
$html_headline[] = 'Ordner verwalten';
	
$inhalt = '<h1>Ordner verwalten</h1>';
if(session_is_registered('userid'))
{
	$query = "SELECT id, ordnername FROM dateien_ordner WHERE besitzer = ".$_SESSION['userid']."";
	$result = mysql_abfrage($query);
	while ($row = mysql_fetch_array($result)) 
	{
		$inhalt .= "<a href=\"admin_view.php?ordner_id=".$row["id"]."\">".$row["ordnername"]."</a><br>";
	}
} else $inhalt = get_anmeldung_link();

echo face($inhalt);
?>