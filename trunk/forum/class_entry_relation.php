<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 include 'class.php';

 class entry_relation extends entry {
 
  function new_entry() {
   return new entry_relation();
  }
  
  function relation_delete() {
   $this->db->query('delete from forum_relation');
  }
  
  function relation_build() {
   $id = $this->data['id'];
   $parent = &$this;
   $level = 0;
   while (isset($parent) && $parent->data['id']) {
    $rel_to = $parent->data['rel_to'];
    $level++;
    $this->db->query('insert into forum_relation values ("'.$rel_to.'","'.$id.'","'.$level.'")');
    $parent = &$this->entries[$rel_to];
   }
   $this->load_answers();
   foreach ($this->answers as $id => $answer) {
    $answer->relation_build();
   }
  }
  
  function load_answers() {
   $query = 'select
              forum.id,
              forum.rel_to
             from forum
             where forum.rel_to="'.$this->data['id'].'"';
   $result = $this->db->query($query);
   while ($data = mysql_fetch_array($result)) {
    $id = $data['id'];
    $answer = &$this->entries[$id];
    if (!isset($answer)) {
     $answer = $this->new_entry();
     $answer->set_data($data);
     $this->answers[$id] = &$answer;
     $answer->set_parent($this);
    }
   }
  }
  
  function set_parent($parent) {
   $this->entries = &$parent->entries;
  }
  
  function rebuild_relation() {
   $this->id = 0;
   $this->load();
   $this->relation_delete();
   $this->relation_build();
  }
  
 }
 
?>
