<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2008 Maikel Linke, Christian Zedler
 */
include 'var.php';

// proofs, wether a person is an array of persons
function personArrayContainsId($personArray,$personId) {
	foreach ($personArray as $i => $person) {
		if ($person['id'] == $personId) {
			return true;
		}
	}
	return false;
}

// f�gt der Datenbank ein todo hinzu
function add_todo($data,$gids=array()){
	$uid = $_SESSION['userid'];
	if (trim($data['name']) == '') $data['name'] = 'todo';
	$data['name'] = text2html($data['name']);
	$data['text'] = format_msg($data['text']);
	if (sizeof($gids)>0) {
		foreach ($gids as $gid => $gname) {
			$member = get_personen($gid);
			if (personArrayContainsId($member,$uid))
			for ($i=0;$i<sizeof($member);$i++)
			$pids[$member[$i]['id']] = 'those who understand binary and those who dont';
		}
	} else $pids[$uid] = '';
	$todo = new todo();
	$todo->data = $data;
	foreach($pids as $pid => $there_are_only_10_types_of_people){
		$todo->create($pid);
	}
}

// gibt alle Personen [einer Gruppe] zurck
function get_personen($gid=0,$buchstabe=0){
	$person = array();
	$query = 'select person.id,person.first_name,person.last_name';
	$query.= ' from person';
	if($gid)
	$query.= ',pg where person.id=pg.pid and pg.gid="'.$gid.'"';
	if($buchstabe){
		if($gid) $query.= ' and';
		else $query.= ' where';
		if($buchstabe == '#') $query.= ' person.last_name regexp "^[0-9]"';
		else $query.= ' person.last_name like "'.$buchstabe.'%" or person.last_name like "&'.$buchstabe.'uml;%"';
	}
	$query.= ' order by last_name';
	global $db;
	$personen = $db->query($query);
	while(list($p['id'],$p['first_name'],$p['last_name']) = mysql_fetch_row($personen)){
		$p['vorname'] = $p['first_name'];
		$p['nachname'] = $p['last_name'];
		$p = complete_name($p);
		$person[] = $p;
	}
	return $person;
}

$output->secure();

$data = $_POST['data'];
if(!checkdate($data['month'],$data['day'],$data['year'])){
	if($data['day']>28) $data['day']=date('t');
	else $data['day'] = date('d');
}
if(checkdate($data['month'],$data['day'],$data['year'])){
	$data['deadline'] = $data['year'].'-'.$data['month'].'-'.$data['day'].' '.$data['hour'].':'.$data['minute'].':'.$data['second'];
	if (!isset($data['expire'])) $data['expire'] = false;
	$ids = array();
	if($_POST['group']){
		foreach ($data as $index => $value) $v['data['.$index.']'] = $value;
		$ids = complete_ids('g','group',$v,$_SESSION['userid']);
	}
	add_todo($data,$ids);
	$_SESSION['calendar_week'] = w_dif($data['deadline']);
	redirect('./');
} else repulse();

?>