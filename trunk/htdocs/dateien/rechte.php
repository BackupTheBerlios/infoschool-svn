<?php
	include 'var.php';
	$html_headline[] = 'Ordnerrechte';
	
	$string[1] = "<table border cellpadding=2 cellspacing=2 width=\"100%\" class=\"forum\">
<tr><th  class=\"forum\" id=\"desc\"><font size=\"-1\">Name</font></th><th  class=\"forum\" id=\"desc\"><font size=\"-1\">Berechtigungen</font></th></tr>";
	
	$string[2] = "<tr>
<td class=\"forum\">%ordnername%<br><font size=\"-1\">%besitzer%</font></td>
<td class=\"forum\">";
	
	
	$string["lesen"] = "<img alt=\"Lesen\" title=\"Lesen\" src=\"../img/r.gif\" border=0><tt>&#160;</tt>";
	$string["schreiben"] = "<img alt=\"Schreiben\" title=\"Schreiben\" src=\"../img/w.gif\" border=0><tt>&#160;</tt>";
	$string["eigenes_editieren"] = "<img alt=\"Eigenes Editieren\" title=\"Eigenes Editieren\" src=\"../img/sed.gif\" border=0><tt>&#160;</tt>";
	$string["eigenes_loeschen"] = "<img alt=\"Eigenes L&ouml;schen\" title=\"Eigenes L&ouml;schen\" src=\"../img/srm.gif\" border=0><tt>&#160;</tt>";
	$string["editieren"] = "<img alt=\"Editieren\" title=\"Editieren\" src=\"../img/ed.gif\" border=0><tt>&#160;</tt>";
	$string["loeschen"] = "<img alt=\"L&ouml;schen\" title=\"L&ouml;schen\" src=\"../img/rm.gif\" border=0><tt>&#160;</tt>";
	$string["rechte_aendern"] = "<img alt=\"Rechte &Auml;ndern\" title=\"Rechte &Auml;ndern\" src=\"../img/chr.gif\" border=0><tt>&#160;</tt>";
	$string["besitzer"] = "<img alt=\"Besitzer\" title=\"Besitzer\" src=\"../img/be.gif\" border=0><tt>&#160;</tt>";

	$query = "SELECT o.id id, o.ordnername, d.ordner_id, count(distinct d.id) anzahl, drp.person_id, drp.recht personenrecht, BIT_OR(drg.recht) gruppenrechte, pg.pid pg_pid, o.besitzer besitzer
FROM dateien_ordner o 
LEFT JOIN dateien_dateien d ON o.id = d.ordner_id 

LEFT JOIN dateien_recht_person drp ON drp.ordner_id = o.id AND drp.person_id = ".$_SESSION['userid']."

LEFT JOIN pg ON pg.pid = ".$_SESSION['userid']." AND drp.recht IS NULL
LEFT JOIN dateien_recht_gruppe drg ON  pg.gid = drg.gruppe_id AND drg.ordner_id = o.id

WHERE (drp.recht > 0) 
OR ((drg.recht > 0) AND (drp.recht IS NULL))
OR (o.besitzer = ".$_SESSION['userid'].")
GROUP BY o.id";

if(session_is_registered('userid')){

	$inhalt = $string[1];
	$result = mysql_abfrage($query);
	while ($row = mysql_fetch_array($result))
	{
		$besitzer = $row["besitzer"];
		$recht = $row["personenrecht"] + $row["gruppenrechte"]; //einer der beiden Werte wird bereits von MySQL auf 0/NULL gesetzt
		
		//Link zum Bearbeiten nur einblenden, wenn der User die benoetigten Rechte besitzt:
		if ((64 & $recht) OR ($besitzer == $_SESSION['userid'])) $ordnername = "<a href=\"rechte_view.php?ordner_id=".$row["id"]."\">".$row["ordnername"]."</a>";
		else $ordnername = $row["ordnername"];
		
		$person = get_person($besitzer);
		$inhalt .= ausgeben($string[2], array(ordnername => $ordnername, besitzer => $person['vorname'].' '.$person['nachname']));
		
		if (1 & $recht)  $inhalt .= $string["lesen"];
		if (2 & $recht)  $inhalt .= $string["schreiben"];
		if (4 & $recht)  $inhalt .= $string["eigenes_editieren"];
		if (8 & $recht)  $inhalt .= $string["eigenes_loeschen"];
		if (16 & $recht) $inhalt .= $string["editieren"];
		if (32 & $recht) $inhalt .= $string["loeschen"];
		if (64 & $recht) $inhalt .= $string["rechte_aendern"];
		
		if ($besitzer == $_SESSION['userid']) $inhalt .= $string["besitzer"];
		
		$inhalt .= "&#160;</td></tr>";
	}
	$inhalt .= "</table>";

}else $inhalt = get_anmeldung_link();

echo face($inhalt);
?>