<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */

 class user {
  var $id = 0;

  function user() {
   $this->identify();
  }

  function identify() {
   if (isset($_GET['PHPSESSID']) || isset($_POST['PHPSESSID']) || isset($_COOKIE['PHPSESSID'])) {
    session_start();
    if (!session_is_registered('userid')) {
     session_destroy();
     setcookie('PHPSESSID','',0,'/');
     redirect();
    }
    $this->id = $_SESSION['userid'];
   }
   else {
    $this->login(true);
   }
  }

  function login($cookie=false) {
   if ($cookie) {
    if (!isset($_COOKIE['userid'])) return false;
    if (!isset($_COOKIE['passwd'])) return false;
    $uid = (int) $_COOKIE['userid'];
    $passwd = '"'.$_COOKIE['passwd'].'"';
    $where = "person.id=$uid AND person.passwd=$passwd";
   }
   else {
    if (!isset($_POST['first_name'])) return false;
    if (!isset($_POST['last_name'])) return false;
    if (!isset($_POST['passwd'])) return false;
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $passwd = '"'.$_POST['passwd'].'"';
    if (!(isset($_COOKIE['passwd']) && $_COOKIE['passwd'] == $_POST['passwd'])) {
     $passwd = 'password('.$passwd.')';
    }
    $where = "person.passwd=$passwd AND person.last_name='$last_name'";
    if ($first_name) $where.= " AND person.first_name='$first_name'";
   }
   $select = "person.id, person.passwd, person.first_name, person.last_name, person.last_login, admin.pid as admin
                  FROM person
                  LEFT JOIN admin
                  ON person.id=admin.pid
                  WHERE $where";
   global $db;
   $db->select($select);
   if ($db->num_rows == 1) {
    session_start();
    $this->set_data($db->data[0]);
    $db->update("person set last_login=now() where id=$this->id");
    $db->delete("todo where pid=$this->id and deadline<now() and expire=1");
    load_opt();
    $origin = './';
    if (isset($_GET['origin']) && $_GET['origin'] != 'logout.php') $origin = $_GET['origin'];
    if (isset($_POST['origin']) && $_POST['origin'] != '/logout.php') $origin = $_POST['origin'];
    redirect($origin);
   }
   return $db->num_rows;
  }

  function set_data($data) {
   $this->id = (int) $data['id'];
   $_SESSION['this'] = 'infoschool';
   $_SESSION['userid'] = $this->id;
   $_SESSION['first_name'] = mask_html($data['first_name']);
   $_SESSION['last_name'] = mask_html($data['last_name']);
   $_SESSION['name'] = str_replace(' ','&nbsp;',mask_html($data['first_name'].' '.$data['last_name'])); // obsolete
   $_SESSION['last_login'] = $data['last_login'];
   $_SESSION['admin'] = false;
   if (isset($data['admin']) && $data['admin']) $_SESSION['admin'] = true;
   load_opt();
   if (isset($_COOKIE['userid']) || (isset($_POST['passwd']) && isset($_POST['save']))) {
    setcookie('userid',$this->id,strtotime('+3 months'),'/');
    setcookie('passwd',$data['passwd'],strtotime('+3 months'),'/');
    setcookie('first_name',$data['first_name'],strtotime('+3 months'),'/');
    setcookie('last_name',$data['last_name'],strtotime('+3 months'),'/');
   }
  }

 }

?>