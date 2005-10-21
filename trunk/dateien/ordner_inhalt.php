<?php
 include 'var.php';
 $html_headline[] = 'Ordneransicht';
	
$string[1] = "<h1>Ordneransicht</h1>
<table width=\"100%\" align=\"center\" cellspacing=\"2\" cellpadding=\"2\" border=\"1\" frame=\"box\">
<tr>
    <td class=\"datei_download_title\" colspan=4><b>%titel%&nbsp;</b></td>
</tr>
<tr>
	<td class=\"datei_download_info\" width=\"40px\" rowspan=2><a href=\"download.php?dateiid=%id%\"><img border=\"none\" src=\"%icon%\"></a></td>
    <td class=\"datei_download_info\"  width=\"70%\" colspan=1><a href=\"download.php?dateiid=%id%\">%dateiname%</a>&nbsp; - %groesse%</td>
	<td class=\"datei_download_info\"  width=\"30%\" align=\"right\">&nbsp;%datum%</td>
</tr>
<tr valign=\"top\"> 
	<td class=\"datei_download_info\">%besitzer%&nbsp;</td>
	<td class=\"datei_download_info\" align=\"right\">&nbsp;%aktionen%</td>	
</tr>
<tr>
	<td class=\"datei_download_text\" colspan=\"4\">%beschreibung%&nbsp;</td>
</tr>
</table><br>";
	
$string[2] = "<a href=\"upload.php?ordner_id=%ordner_id%\"><img alt=\"Schreiben\" title=\"Schreiben\" src=\"../img/w.gif\" border=0> Datei hinzuf&uuml;gen</a>";

$string["eigenes_editieren"] = "<img alt=\"Eigenes Editieren\" title=\"Eigenes Editieren\" src=\"../img/sed.gif\" border=0><tt>&#160;</tt>";
$string["eigenes_loeschen"] = "<a href=\"".$PHP_SELF."?modus=loeschen&datei_id=".$datei_id."\"><img alt=\"Eigenes L&ouml;schen\" title=\"Eigenes L&ouml;schen\" src=\"../img/srm.gif\" border=0><tt>&#160;</tt></a>";
$string["editieren"] = "<img alt=\"Editieren\" title=\"Editieren\" src=\"../img/ed.gif\" border=0><tt>&#160;</tt>";
$string["loeschen"] = "<a href=\"".$PHP_SELF."?modus=loeschen&datei_id=".$datei_id."\"><img alt=\"L&ouml;schen\" title=\"L&ouml;schen\" src=\"../img/rm.gif\" border=0></a><tt>&#160;</tt>";
	

function format_dateigroesse($groesse)
{
	if ($groesse < 1024)
	{
	$groesse = $groesse.' Byte';
	}
	elseif ($groesse < 1048576)
	{
	$groesse = round($groesse / 1024, 0);
	$groesse = $groesse.' KB';
	}
	else
	{
	$groesse = round($groesse / 1048576, 0);
	$groesse = $groesse.' MB';
	}
	$groesse = str_replace('.',',',$groesse);
	return $groesse;
}

function get_icon($name, $mime)
{
$array = explode('.',$name);
$endung = strtolower($array[count($array)-1]);

if ($endung == 'htm') $endung = 'html';
if ($endung == 'jpeg') $endung = 'jpg';

$vorhanden = array('bmp', 'chm', 'csv', 'dfm', 'dpr', 'doc', 'exe', 'gif', 'gz', 'html', 'jpg', 'mmp', 'mp3', 'pap', 'pas', 'pdf', 'php', 'ppt', 'rtf', 'sxc', 'sxw', 'txt', 'xls', 'zip');
for ($i=0;$i<count($vorhanden)-1;$i++)
	{
	if ($vorhanden[$i] == $endung)
		{
		$moeglich = true;
		}
	}

if ($moeglich) $return = $endung.'.gif';
else
	{
	switch ($mime) {
    	case "text/plain":
        	$return = "txt.gif";
	        break;
    	case "application/pdf":
        	$return = "pdf.gif";
	        break;
    	case "image/jpeg":
        	$return = "jpg.gif";
	        break;
    	case "application/x-msdownload":
        	$return = "exe.gif";
	        break;
    	case "text/html":
	        $return = "html.gif";
    	    break;
	    case "application/msexcel":
    	    $return = "xls.gif";
        	break;
	    case "application/msword":
    	    $return = "doc.gif";
        	break;
	    case "image/gif":
    	    $return = "gif.gif";
        	break;
	    case "application/rtf":
    	    $return = "rtf.gif";
        	break;
	    case "text/comma-separated-values":
    	    $return = "csv.gif";
        	break;
	    case "application/x-gzip":
    	    $return = "gz.gif";
        	break;
	    case "application/vnd.ms-powerpoint":
    	    $return = "ppt.gif";
        	break;

		default:
			$return = "unbekannt.gif";
		}
	}
	
return $GLOBALS['root']."/img/icons/".$return;
}

if(session_is_registered('userid')){


if (!isset($ordner_id))
	{
	header ("Location: index.php");
	exit;
	}
//�erprfen ob �derungsrechte vorhanden sind/ Benutzer der Besitzer ist:
if (!hat_recht($ordner_id,1)) die("Fehler"); 

	$recht = return_recht($ordner_id);

if ($modus == "loeschen")
{
	$query = "SELECT besitzer FROM dateien_dateien WHERE id = $datei_id";
	$result = mysql_abfrage($query);
	$row = mysql_fetch_array($result);
	if ((($recht & 8) AND ($row['besitzer'] == $_SESSION['userid'])) OR ($recht & 32))
	{
	datei_loeschen($datei_id);
	}
	$_SESSON['meldungen'] ="nix";
	header ("Location: $PHP_SELF?ordner_id=$ordner_id");
	exit;
}

else {

	//Ordnername als �erschrift ausgeben
	$query = "SELECT ordnername FROM dateien_ordner WHERE id = $ordner_id";
	$result = mysql_abfrage($query);
	$row = mysql_fetch_array($result);
	$inhalt = "<h2>".$row['ordnername']."</h2>";
	//Ordnername als Titel hinzufgen
	$html_titel[] = $row['ordnername'];

		$query = "SELECT d.id, d.titel, d.dateiname, d.dateityp, d.beschreibung, d.besitzer, d.datum, d.groesse, person.first_name, person.last_name FROM dateien_dateien d LEFT JOIN person ON d.besitzer = person.id WHERE d.ordner_id = $ordner_id";

		$result = mysql_abfrage($query);
		while ($row = mysql_fetch_array($result)) 
		{
		$datei_id = $row['id'];
		$aktionen = "";
		if ($row['besitzer'] == $_SESSION['userid'])
		{
			if ($recht & 4) $aktionen .= "<a href=\"datei_aendern.php?datei_id=".$datei_id."\"><img alt=\"Eigenes Editieren\" title=\"Eigenes Editieren\" src=\"../img/sed.gif\" border=0></a><tt>&#160;</tt>";
			if ($recht & 8) $aktionen .= "<a href=\"".$PHP_SELF."?modus=loeschen&datei_id=".$datei_id."&ordner_id=".$ordner_id."\"><img alt=\"L&ouml;schen\" title=\"L&ouml;schen\" src=\"../img/srm.gif\" border=0></a><tt>&#160;</tt>";
		}
		else 
		{
			if ($recht & 16) $aktionen .= "<a href=\"datei_aendern.php?datei_id=".$datei_id."\"><img alt=\"Editieren\" title=\"Editieren\" src=\"../img/ed.gif\" border=0></a><tt>&#160;</tt>";
			if ($recht & 32) $aktionen .= "<a href=\"".$PHP_SELF."?modus=loeschen&datei_id=".$datei_id."&ordner_id=".$ordner_id."\"><img alt=\"L&ouml;schen\" title=\"L&ouml;schen\" src=\"../img/rm.gif\" border=0></a><tt>&#160;</tt>";
		}
		
		$datum = htmlformat_datum_neu($row['datum']);
		$groesse = format_dateigroesse($row['groesse']);
		
		$icon = get_icon($row["dateiname"], $row["dateityp"]);
		
		$besitzer = "von: <a href=\"".$GLOBALS["root"]."/users/person.php?id=".$row["besitzer"]."\">".$row["first_name"]." ".$row["last_name"]."</a>";
		
		$inhalt .= ausgeben($string[1], array(id => $row["id"], 
												titel => $row["titel"], 
												dateiname => $row["dateiname"], 
												groesse => $groesse, 
												dateityp => $row["dateityp"],
												icon => $icon,
												datum => $datum, 
												beschreibung => $row["beschreibung"], 
												besitzer => $besitzer,
												aktionen => $aktionen ));
		}
	
	if ($recht & 2) $inhalt .= ausgeben($string[2], array(ordner_id => $ordner_id));
}

}else $inhalt = get_anmeldung_link();
echo face($inhalt);
?>
