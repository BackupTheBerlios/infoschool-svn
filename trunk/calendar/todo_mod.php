<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */
include 'var.php';
require_once 'class_Date.php';

// changes an todo entry
function mod_todo($data,$origin){
	$validDate = new Date($data['year'], $data['month'], $data['day']);
	$data['deadline'] = $validDate->toString().' '.$data['hour'].':'.$data['minute'].':'.$data['second'];
	if (trim($data['name']) == '') $data['name'] = 'todo';
	$data['name'] = text2html($data['name']);
	$data['text'] = format_msg($data['text']);
	if(!isset($data['expire'])) $data['expire'] = false;
	$t = new todo();
	$t->data = $data;
	$t->save();
	$_SESSION['calendar_week'] = w_dif($data['deadline']);
	redirect('todo.php?tid='.$data['id']);
}

$output->secure();

if (isset($_POST['data'])) {
	mod_todo($_POST['data'],$_POST['origin']);
} else repulse();

?>