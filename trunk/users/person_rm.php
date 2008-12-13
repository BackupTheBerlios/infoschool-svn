<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
include 'var.php';

function person_rm($pid) {
	if (!$pid) return false;
	global $db;
	$datei = $GLOBALS['img_person_dir']['intern'].$pid;
	if (file_exists($datei.'.jpg')) unlink($datei.'.jpg');
	if (file_exists($datei.'.gif')) unlink($datei.'.gif');
	$db->select('id from gruppe where leiter='.$pid);
	$groups = $db->data;
	person_rm_groups($groups);
	person_rm_forum($pid);
	person_rm_calendar($pid);
	person_rm_messages($pid);
	person_rm_zensuren($pid);
	person_rm_files($pid);
	person_rm_news($pid);
	$db->delete('person where id="'.$pid.'"');
	$db->delete('admin where pid='.$pid);
	$db->delete('pg where pid='.$pid);
	$db->delete('neu_pg where pid='.$pid);
	return true;
}

function person_rm_groups($groups) {
	foreach ($groups as $i => $groupdata) {
		$group = new group($groupdata['id']);
		$group->delete();
	}
}

function person_rm_forum($pid) {
	include_once '../forum/class_entry_delete.php';
	global $db;
	$db->select('id from forum where author='.$pid.' order by id DESC');
	$fora = $db->data;
	foreach ($fora as $i => $forum) {
		$entry = new entry_delete();
		$entry->id = $forum['id'];
		$entry->load();
		$entry->load_answers(null);
		$entry->delete();
	}
	$db->delete('forum_rights_person where person_id="'.$pid.'"');
}

function person_rm_calendar($pid) {
	global $db;
	$db->delete('todo where pid='.$pid);
}

function person_rm_messages($pid) {
	global $db;
	$db->select('messages.id
              from messages
              left join person as author on
               messages.author=author.id 
              join messages_to on
               messages.id=messages_to.message
               and (
                author.id="'.$pid.'" or
                author.id is null
               ) 
              left join person as person_to on
               messages_to.person=person_to.id 
              where
               person_to.id="'.$pid.'" or
               person_to.id is null
              ');
	$messages = $db->data;
	foreach ($messages as $i => $m) {
		$db->query('delete from messages where id="'.$m['id'].'"');
		$db->query('delete from messages_to where message="'.$m['id'].'"');
	}
}

function person_rm_zensuren($pid) {
	global $db;
	$db->delete('zensuren where pid='.$pid);
}

function person_rm_files($pid) {
	include_once '../files/class.php';
	global $db;
	$db->select('id from filesystem where owner="'.$pid.'"');
	$items = $db->data;
	foreach ($items as $i => $item_row) {
		$fs_item = new fs_item($item_row['id']);
		if ($fs_item->data['id'] == $item_row['id']) {
			$fs_item->delete();
		}
	}
}

function person_rm_news($pid) {
	global $db;
	$db->delete('news_level_person where person_id="'.$pid.'"');
}

// je nach den in in $p['opt'] gesetzten Bits werden Informationen zurckgegeben function filter_person($p){
function filter_person($p){
	if (isset($p['first_name']) && !isset($p['vorname'])) $p['vorname'] = $p['first_name'];
	if (isset($p['last_name']) && !isset($p['nachname'])) $p['nachname'] = $p['last_name'];
	if (!isset($p['first_name']) && isset($p['vorname'])) $p['first_name'] = $p['vorname'];
	if (!isset($p['last_name']) && isset($p['nachname'])) $p['last_name'] = $p['nachname'];
	$public = decbin($p['opt']);
	while(strlen($public)<8)$public='0'.$public;
	$person['id'] = $p['id'];
	$person['vorname'] = $p['vorname'];
	$person['nachname']= $p['nachname'];

	if (isset($p['icq']))
	$person['icq']= $p['icq'];
	if (isset($p['homepage']))
	$person['homepage']= $p['homepage'];
	if (isset($p['klasse']))
	$person['klasse']= $p['klasse'];
	if (isset($p['beschreibung']))
	$person['beschreibung']= $p['beschreibung'];

	if(isset($p['nk'])) $person['nk'] = $p['nk'];
	if($public[7])$person['gebdat']=$p['gebdat'];
	if($public[6])$person['mail']=$p['mail'];
	return $person;
}

// holt Informationen ber eine Person aus der Datenbank
function get_person($pid){
	$q = 'select id,first_name,last_name';
	$q.= ' from person where id="'.$pid.'"';
	global $db;
	$personenrow = $db->query($q);
	if(mysql_num_rows($personenrow)>1){
		return 'Es wurde mehr als eine Person gefunden.';
	}
	else{
		return mysql_fetch_array($personenrow);
	}
}

$output->secure();
$pid = $user->id;
if (is_admin()){
	if (isset($_POST['id'])) $pid = (int) $_POST['id'];
}

$person = get_person($pid);
if (!is_array($person)) redirect('./');

$assured = false;
if (isset($_POST['assured'])) $assured = $_POST['assured'];

if ($assured) {
	$removed = person_rm($pid);
	if ($removed) {
		if ($pid == $user->id) session_destroy();
		$tmpl = new tmpl('person_removed.html');
	}
	else die('ERROR: not removed');
}
else {
	$tmpl = new tmpl('person_rm.html',$person);
}


$output->out($tmpl);
?>