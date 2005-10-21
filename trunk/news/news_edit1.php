<?php
 include 'var.php';
 $html_titel[] = '';
 
 if(session_is_registered('userid')){


	$query = "SELECT * FROM news_eintraege WHERE id=$id";
	$result = mysql_abfrage($query);
	$row = mysql_fetch_array($result);

	if (
		($row['ersteller_id'] != $_SESSION['userid']) 
		AND 
			(
			($row['level'] > get_level($_SESSION['userid'])) 
			OR
			(get_level($_SESSION['userid']) == 0)
			)
		) redirect('index.php');

	$datum = strtotime($row["datum"]);

	$v['%datum_tag%'] = get_select_int('datum[tag]',1,32,date(j,$datum));
  	$v['%datum_monat%'].= get_select_int('datum[monat]',1,13,date(n,$datum));
  	$v['%datum_jahr%'].= get_select_int('datum[jahr]',date(Y)-10,date(Y)+10,date(Y,$datum));
  	$v['%zeit_stunde%'].= get_select_int('zeit[stunde]',0,24,date(H,$datum));
  	$v['%zeit_minute%'].= get_select_int('zeit[minute]',0,60,date(i,$datum));
	
	if ($row['ort_infoschool']) $v['%ort_infoschool%'] = 'checked';
	else $v['%ort_infoschool%'] = '';

	if ($row['ort_homepage']) $v['%ort_homepage%'] = 'checked';
	else $v['%ort_homepage%'] = '';
	

	$user_level = get_level($_SESSION['userid']);

	if ($row['level'] > $user_level)
		{
		$v['%level%'] = get_select_int('level',0,$user_level+1,$user_level);
		$v['%level%'] .= ' <font color="red">Wenn Sie den Eintrag bearbeiten wird der Level auf '.$user_level.' zur&uuml;ckgesetzt!</font>';
		}
	else $v['%level%'] = get_select_int('level',0,$user_level+1,$row['level']);
	
	$v['%kategorie%'] = get_select_str('kategorie', array('News', 'Update', 'Termin'),$row['kategorie']);
	$v['%status%'] = get_select_str('status', array('Bearbeitung', 'Aktiv', 'Archiv'),$row['status']);

	$v['%titel%'] = $row["titel"];
	$v['%eintrag%'] = htmlformat_textarea(html2textarea($row["eintrag"]),50,10);
	$v['%kommentar%'] = html2textarea($row["kommentar"]);
	$v['%initial%'] = $row["initial"];
	$v['%link%'] = $row["link"];
	$v['%id%'] = $row["id"];

	$input = get_face('news_neu_form.html',$v);
	$inhalt = get_form($input,'&Auml;ndern','news_edit2.php');

 }else $inhalt = get_anmeldung_link();
  echo face($inhalt);
?>