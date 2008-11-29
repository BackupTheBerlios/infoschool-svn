<?php

require_once $root.'class_file.php';

// gibt ein Select-Feld zurck, das nach Zahlen fragt
function get_select_int($name,$start,$end,$value){
	$select = '<select name="'.$name.'">';
	for ($i=$start;$i<$end;$i++){
		$select.= '<option value="'.$i.'"';
		if ($i == $value) $select.= ' selected';
		$select.= '>'.$i.'</option>';
	}
	$select.= '</select>';
	return $select;
}

// berprft, ob jemand eine Mitgliedschaft beantragt hat
function in_neu_pg($pid,$gid){
	global $db;
	$neu = $db->query('select pid from neu_pg where pid="'.$pid.'" and gid="'.$gid.'"');
	return mysql_num_rows($neu);
}


// gibt ein Formular fr eine neue Person zurck
function get_neu_person_form($person){
	$dir = $GLOBALS['root'].'benutzer/';
	if(isset($person['accept'])) $person['accept'] = ' checked';
	else $person['accept'] = '';
	if(!isset($person['vorname'])) $person['vorname'] = '';
	if(!isset($person['nachname'])) $person['nachname'] = '';
	if(!isset($person['gebtag'])) $person['gebtag'] = '';
	if(!isset($person['gebmon'])) $person['gebmon'] = '';
	if(!isset($person['mail'])) $person['mail'] = '';
	if(!isset($person['passwd1'])) $person['passwd1'] = '';
	if(!isset($person['passwd2'])) $person['passwd2'] = '';
	$vars['%bedingungen%'] = FileReader::readFile($dir.'bedingungen.html');
	$vars['%accept%'] = $person['accept'];
	$vars['%vorname%'] = text2html($person['vorname']);
	$vars['%nachname%'] = text2html($person['nachname']);
	if(isset($person['gebjahr']))$gebjahr=$person['gebjahr'];
	else $gebjahr = date('Y')-12;
	$gebdat = get_select_int('person[gebtag]',1,32,$person['gebtag']);
	$gebdat.= get_select_int('person[gebmon]',1,13,$person['gebmon']);
	$gebdat.= get_select_int('person[gebjahr]',date('Y')-65,date('Y')-5,$gebjahr);
	$vars['%gebdat%'] = $gebdat;
	$vars['%mail%'] = $person['mail'];
	$vars['%passwd1%'] = text2html($person['passwd1']);
	$vars['%passwd2%'] = text2html($person['passwd2']);
	$form = FileReader::readFile($dir.'neu_person.html',$vars);
	return $form;
}

?>