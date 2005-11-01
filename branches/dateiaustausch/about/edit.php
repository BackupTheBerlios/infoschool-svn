<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';
 
 function about_save($name,$text) {
  global $db;
  $db->update('about set text="'.$text.'" where name="'.$name.'"');
  redirect('./');
 }

 $output->secure('admin');
 $output->headline[] = 'About';
 
 $edit = $_GET['edit'];
 
 if (isset($_POST['text'])) {
  about_save($edit,$_POST['text']);
 }
 
 $v['edit'] = $edit;
 $v['text'] = about($edit);
 
 $content = new tmpl('edit.html',$v);

 $output->out($content);
?>