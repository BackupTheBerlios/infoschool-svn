<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 function send_auth($first,$last) {
  global $db;
  $query = 'id, first_name, last_name, mail, passwd from person where first_name="'.$first.'" and last_name="'.$last.'"';
  $db->select($query);
  if ($db->num_rows != 1) return 'name not found';
  $person = $db->data[0];
  $tmpl = new tmpl('mail_auth_key_subject.txt');
  $subject = $tmpl->fdata;
  $v['key'] = $person['passwd'];
  $v['link'] = $GLOBALS['http_root'].'users/passwd_new.php?pid='.$person['id'].'&key='.$person['passwd'];
  $tmpl = new tmpl('mail_auth_key.txt',$v);
  $data = $tmpl->fdata;
  $mail = new mail($person['mail'],$person['first_name'].' '.$person['last_name'],$subject,$data,false,true);
  $return = 'mail not sent';
  if ($mail->sent) $return = 'mail sent';
  return $return;
 }

 function send_passwd($pid,$key) {
  global $db;
  $query = 'first_name, last_name, mail from person where id="'.$pid.'" and passwd="'.$key.'"';
  $db->select($query);
  if ($db->num_rows != 1) return false;
  $person = $db->data[0];
  $to = $person['first_name'].' '.$person['last_name'];
  $tmpl = new tmpl('mail_new_passwd_subject.txt');
  $subject = $tmpl->fdata;
  $v['passwd'] = rand_passwd();
  $tmpl = new tmpl('mail_new_passwd.txt',$v);
  $data = $tmpl->fdata;
  $mail = new mail($person['mail'],$to,$subject,$data,false,true);
  if ($mail->sent) {
   $query = 'person set passwd=old_password("'.$v['passwd'].'") where id="'.$pid.'"';
   $db->update($query);
  }
  return $mail->sent;
 }

 function rand_passwd() {
  $consonants = 'bcdfghjklmnpqrstvw';
  $vowels = 'aeiou';
  $numbers = '0123456789';
  srand((double)microtime()*1000000);
  $passwd = $consonants[rand(0,strlen($consonants)-1)];
  $passwd.= $vowels[rand(0,strlen($vowels)-1)];
  $passwd.= $consonants[rand(0,strlen($consonants)-1)];
  $passwd.= $vowels[rand(0,strlen($vowels)-1)];
  $passwd.= $consonants[rand(0,strlen($consonants)-1)];
  $passwd.= $vowels[rand(0,strlen($vowels)-1)];
  $passwd.= $numbers[rand(0,strlen($numbers)-1)];
  $passwd.= $numbers[rand(0,strlen($numbers)-1)];
  return $passwd;
 }

 $v['step'] = '0';
 $v['error'] = 'no error';

 if (isset($_POST['first_name']) && isset($_POST['last_name'])) {
  $status = send_auth($_POST['first_name'],$_POST['last_name']);
  if ($status == 'mail sent') {
   $v['step'] = '1';
  }
  else {
   $v['error'] = $status;
  }
 }

 if (isset($_GET['pid']) && isset($_GET['key'])) {
  $sent = send_passwd((int) $_GET['pid'],$_GET['key']);
  if ($sent) {
   $v['step'] = '2';
  }
  else {
   $v['error'] = 'mail not sent';
  }
 }

 $v['step0'] = array();
 if ($v['step'] == '0') $v['step0'][] = array();

 $content = new tmpl('passwd_new.html',$v);

 $output->out($content);
?>