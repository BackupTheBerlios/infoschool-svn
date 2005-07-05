<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 function person_new($data) {
  global $db;
  $query = 'neu_account (passwd,vorname,nachname,mail) values ';
  $query.= '(password("'.$data['passwd1'].'"),"'.$data['first_name'].'","'.$data['last_name'].'","'.$data['mail'].'")';
  $db->insert($query);
 }

 $data = array(
  'agree' => array(),
  'first_name' => '',
  'last_name' => '',
  'passwd1' => '',
  'passwd2' => '',
  'mail' => '',
 );
 $error = array();

 if (isset($_POST['person'])) {
  $data = $_POST['person'];
  if (!isset($_POST['agree'])) $error[]['error'] = 'not agreed';
  if (!$data['first_name'] && !$data['last_name']) $error[]['error'] = 'no name';
  if ($data['passwd1'] != $data['passwd2']) $error[]['error'] = 'different passwords';
  if (count($error) == 0) {
   person_new($data);
   redirect('person_new_request.php?done=1');
  }
 }


 $tmpl = 'person_new_request.html';
 if (isset($_GET['done'])) {
  $tmpl = 'person_new_request_done.html';
 }


 $v = $data;
 $v['agree'] = array();
 if (isset($_POST['agree'])) $v['agree'][] = array();
 $v['error'] = $error;

 $content = new tmpl($tmpl,$v);

 $output->out($content);
?>