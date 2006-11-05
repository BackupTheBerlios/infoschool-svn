<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 
 include_once 'class.php';
 
 class entry_new extends entry {
  
  function load_new() {
   $this->load_read();
   $pid = $_SESSION['userid'];
   $query = 'select
              forum_relation.level, 
              forum.id,
              forum.rel_to,
              forum.author, 
              person.first_name as author_first_name, 
              person.last_name as author_last_name, 
              forum.created, 
              forum.topic, 
              forum.text, 
              rights_person.rights as rights_person, 
              pg.gid, 
              rights_group.rights as rights_group 
             from forum_relation
             left join forum on
              forum_relation.answer=forum.id 
             left join person on
              forum.author=person.id 
             left join forum_rights_person as rights_person on
              forum.id=rights_person.entry_id 
              and rights_person.person_id="'.$pid.'" 
             left join forum_rights_group as rights_group on
              rights_person.rights is null 
              and forum.id=rights_group.entry_id 
             left join pg on
              rights_group.group_id=pg.gid 
              and pg.pid="'.$pid.'" 
             where
               forum.created>"'.$_SESSION['last_login'].'"';
   $query.= 'order by
              forum_relation.level,
              forum.created';
   $result = $this->db->query($query);
   while ($data = mysql_fetch_array($result)) {
    $level = $data['level'];
    $id = $data['id'];
    $entry = &$this->entries[$id];
    if (!isset($entry)) {
     $entry = new entry();
     $entry->set_data($data);
     if (!isset($this->read[$id])) {
      $entry->new = true;
     } 
    }
    $entry->add_rights_row($data);
   }
   $this->index_new();
  }
  
  function index_new() {
   $this->entries[0] = &$this;
   $new_entries = $this->entries;
   foreach ($new_entries as $id => $entry_copy) {
    $entry = &$this->entries[$id];
    if (!$entry->new) continue;
    $entry->load_history();
    $entry->user_rights();
    if (!$entry->right_read()) continue;
    $this->new_answers++;
    $i = 0;
    $i_max = count($entry->history);
    if ($i_max == 1) {
     $parent = &$entry;
    }
    else {
     $answer = &$entry;
     do {
      $i++;
      $parent = &$entry->history[$i];
      if (!$parent->id) {
      }
      if (!isset($this->entries[$parent->id])) {
       $this->entries[$parent->id] = $parent;
      }
      $parent = &$this->entries[$parent->id];
      $parent->answers[$answer->id] = &$answer;
      $answer = &$parent;
     }
     while ($i < $i_max && $parent->new);
    } 
    $this->answers[$parent->id] = &$parent;
   }
  }
  
 }
 
?>
