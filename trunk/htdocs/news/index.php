<?php
include 'var.php';
$html_headline[] = 'Newseintr&auml;ge';

if(session_is_registered('userid')){

	#Navigationsleiste
	$v['%status%'] = get_select_str('anzeigen[status]', array('Alle', 'Bearbeitung', 'Aktiv', 'Archiv'),$_POST['anzeigen']['status']);
	$v['%ort%'] .= get_select_str('anzeigen[ort]', array('Alle', 'Beide', 'Keiner', 'Homepage', 'Infoschool'),$_POST['anzeigen']['ort']);
	$v['%level%'] .= get_select_str('anzeigen[level]',array('Alle','0','1','2'),$_POST['anzeigen']['level']);
	$inhalt = get_face('news_admin_navigation.html',$v);

	$where = 'WHERE ';
	
	if (($_POST['anzeigen']['status'] == 'Bearbeitung') OR ($_POST['anzeigen']['status'] == 'Aktiv') OR ($_POST['anzeigen']['status'] == 'Archiv'))
		$where .= 'status="'.$_POST['anzeigen']['status'].'" AND ';
	
	if ($_POST['anzeigen']['ort'] == 'Beide') $where .= '(ort_homepage="1" AND ort_infoschool="1") ';
	elseif ($_POST['anzeigen']['ort'] == 'Keiner') $where .= '(ort_homepage="0" AND ort_infoschool="0") ';
	elseif ($_POST['anzeigen']['ort'] == 'Infoschool') $where .= 'ort_infoschool="1" ';
	elseif ($_POST['anzeigen']['ort'] == 'Homepage') $where .= 'ort_homepage="1" ';
	else $where .= '1=1 ';

	if (($_POST['anzeigen']['level'] == '0') OR ($_POST['anzeigen']['level'] == '1') OR ($_POST['anzeigen']['level'] == '2'))
		$where .= 'AND level = "'.$_POST['anzeigen']['level'].'" ';

	$query = "SELECT * FROM news_eintraege $where ORDER BY datum, titel ASC";
	$result = mysql_abfrage($query);
	
	while ($row = mysql_fetch_array($result)) {
	if (
		($row['ersteller_id'] == $_SESSION['userid']) 
		OR 
		(($row['level'] <= get_level($_SESSION['userid'])) AND (get_level($_SESSION['userid']) > 0))
		) {


		$v['%user%'] = get_face('news_user.html', news_formatieren($row));
		
		$user_level = get_level($_SESSION['userid']);
		if ($row['level'] > $user_level)
			{
			$v['%level%'] = $row['level'];
			if ($row['ort_infoschool']) $v['%ort_infoschool%'] = 'ja';
			else $v['%ort_infoschool%'] = 'nein';
			
			if ($row['ort_homepage']) $v['%ort_homepage%'] = 'ja';
			else $v['%ort_homepage%'] = 'nein';
			
			$v['%kategorie%'] = $row['kategorie'];
			}
		else 
			{
			$v['%level%'] = get_select_int('level',0,$user_level+1,$row['level']);
			if ($row['ort_infoschool']) $v['%ort_infoschool%'] = '<input type="checkbox" name="ort_infoschool" value="infoschool" checked>';
			else $v['%ort_infoschool%'] = '<input type="checkbox" name="ort_infoschool" value="infoschool">';
			
			if ($row['ort_homepage']) $v['%ort_homepage%'] = '<input type="checkbox" name="ort_homepage" value="homepage" checked>';
			else $v['%ort_homepage%'] = '<input type="checkbox" name="ort_homepage" value="homepage">';
			
			$v['%kategorie%'] = get_select_str('kategorie', array('News', 'Update', 'Termin'),$row['kategorie']);
			}
		
		$v['%id%'] = $row['id'];
		
		$v['%status%'] = get_select_str('status', array('Bearbeitung', 'Aktiv', 'Archiv'),$row['status']);
		$v['%kommentar%'] = $row["kommentar"];
		
		$person = get_person($row['ersteller_id']);
		if (is_array($person))
			$v['%ersteller%'] = '<a href="'.$root.'users/person.php?id='.$row['ersteller_id'].'">'.$person['vorname'].'&nbsp;'.$person['nachname'].'</a>';
		else $v['%ersteller%'] = "";
		
		$v['%edit%'] = '<a href="news_rm.php?id='.$row['id'].'">l&ouml;schen</a> - <a href="news_edit1.php?id='.$row['id'].'">editieren</a>';

	$inhalt .= get_face('news_admin.html', $v);

	}
	}


}else $inhalt = get_anmeldung_link();
  echo face($inhalt);
?>
