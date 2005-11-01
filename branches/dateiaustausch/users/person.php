<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->secure();

 $person = new person();

 $content = '';
 if (isset($_GET['id'])){
  $id = (int) $_GET['id'];
  if (!$person->load($id)) $person->load();
  $person->format_public();                
  $content = new tmpl('person.html',$person->public_fdata);
 }
 else {
  if (isset($_POST['data'])) {
   $person->data = $_POST['data'];
   $person->save();
  }
  $person->load();
  $person->format();
  $content = new tmpl('person_mod.html',$person->fdata);
 }

 $output->out($content);
?>