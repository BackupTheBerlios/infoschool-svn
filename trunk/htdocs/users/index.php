<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */
 include 'var.php';

 function list_type(&$data,$type) {
  $data[$type] = array();
  $string = '';
  $ids = array();
  if (isset($_POST[$type])) {
   $string = $_POST[$type];
   if ($string) $ids = find_name($type,$string);
   foreach ($ids as $id => $name) {
    $data[$type][] = array(
      'id' => $id,
      'name' => $name,
    );
   }
  }
  $data[$type.'_name'] = stripslashes(mask_html($string));
  $data[$type.'_count'] = count($ids);
 }

 $output->secure();
 $output->headline[] = 'users';

 $vars = array();
 list_type($vars,'person');
 list_type($vars,'group');

 $content = new tmpl('index.html',$vars);

 $output->out($content);
?>