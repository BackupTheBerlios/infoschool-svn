<?php
 include 'var.php';
 $html_titel[] = '';

 if(session_is_registered('userid')){

	//Daten für die Ausgabe auf die Defaultwerte setzen
	$v['%datum_tag%'] = get_select_int('datum[tag]',1,32,date(j));
  	$v['%datum_monat%'].= get_select_int('datum[monat]',1,13,date(n));
  	$v['%datum_jahr%'].= get_select_int('datum[jahr]',date(Y)-10,date(Y)+10,date(Y));
  	$v['%zeit_stunde%'].= get_select_int('zeit[stunde]',0,24,date(G));
  	$v['%zeit_minute%'].= get_select_int('zeit[minute]',0,60,date(i));

	$level = get_level($_SESSION['userid']);
	$v['%level%'] =  get_select_int('level',0,$level+1,$level);

	$v['%kategorie%'] = get_select_str('kategorie', array('News', 'Update', 'Termin'));
	$v['%status%'] = get_select_str('status', array('Bearbeitung', 'Aktiv', 'Archiv'));
	$v['%titel%'] = '';
	$v['%initial%'] = '';
	$v['%link%'] = '';
	$v['%eintrag%'] = htmlformat_textarea('',50,10);
	$v['%kommentar%'] = '';
	$v['%ort_infoschool%'] = '';
	$v['%ort_homepage%'] = '';

	$input = get_face('news_neu_form.html',$v);
  	$inhalt = get_form($input,'Erstellen','news_erstellen.php');


 }else $inhalt = get_anmeldung_link();
  echo face($inhalt);
?>