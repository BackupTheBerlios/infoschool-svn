<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */
 include 'var.php';
  exit;
 $query = 'id, first_name, last_name, description from person';
 $db->select($query);

 foreach ($db->data as $i => $person) {
  $id = $person['id'];
//  $first = addslashes(html2text($person['first_name']));
//  $last = addslashes(html2text($person['last_name']));
//  $description = addslashes(html2text($person['description']));
//  $c = utf8_encode('ß');
  $c = 'Ã©';
  $first = addslashes(str_replace(utf8_encode($c),$c,$person['first_name']));
  $last = addslashes(str_replace(utf8_encode($c),$c,$person['last_name']));
  $description = addslashes(str_replace(utf8_encode($c),$c,$person['description']));
  $query = "person set
                  first_name='$first',
                  last_name='$last',
                  description='$description'
                 where id='$id'";
  $db->update($query);
 }



?>
