<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 function passwd($passwd) {
  if ($passwd[1] != $passwd[2]) {
   $_SESSION['error'][] = 'different passwords';
   return false;
  }
  if ($passwd[0] == $passwd[1]) {
   $_SESSION['error'][] = 'new and old password are the same';
   return false;
  }
  global $db;
  $uid = $_SESSION['userid'];
  $update = 'person set passwd=password("'.$passwd[1].'") where id="'.$uid.'" and passwd=password("'.$passwd[0].'")';
  $db->update($update);
  if ($db->affected_rows == 0) {
   $_SESSION['error'][] = 'password not changed';
   return false;
  }
  if ($db->affected_rows == 1) {
   $_SESSION['notice'][] = 'password changed';
   return true;
  }
 }

 $output->secure();

 if (isset($_POST['passwd'])) {
  passwd($_POST['passwd']);
 }
 redirect('person.php');
?>