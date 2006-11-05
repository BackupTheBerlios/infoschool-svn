<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 include 'var.php';

 $output->secure();
 
 class entry_delete extends entry {
  
  function new_entry() {
   return new entry_delete();
  }
  
  function delete() {
   foreach ($this->answers as $answer_id => $answer) {
    $answer->delete();
   }
   $id = $this->data['id'];
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
 
 $entry = new entry_delete();
 $entry->get();
 $entry->load();
 $entry->check_right('delete');
 $entry->load_answers(null);
 
 if (isset($_POST['assured']) && $_POST['assured']) {
  $entry->delete();
  redirect('entry.php?id='.$entry->data['rel_to']);
 }

 $entry->format();

 $v = $entry->fdata;

 $content = new tmpl('delete.html',$v);

 $output->out($content);
?>
