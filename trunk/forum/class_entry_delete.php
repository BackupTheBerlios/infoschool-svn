<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 
 include_once 'class.php';
 
 class entry_delete extends entry {
  
  function new_entry() {
   return new entry_delete();
  }
  
  function delete() {
   foreach ($this->answers as $answer_id => $answer) {
    $answer->delete();
   }
   $id = $this->data['id'];
   if ($id) {
    $query = 'forum_rights_person where entry_id="'.$id.'"';
    $this->db->delete($query);
    $query = 'forum_rights_group where entry_id="'.$id.'"';
    $this->db->delete($query);
    $query = 'forum_relation where entry="'.$id.'" or answer="'.$id.'"';
    $this->db->delete($query);
    $query = 'forum where id="'.$id.'"';
    $this->db->delete($query);
   }
  }

 }
 
?>
