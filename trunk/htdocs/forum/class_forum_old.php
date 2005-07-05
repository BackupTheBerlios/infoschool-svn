<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 class forum {
  var $db;
  var $entries = array();

  function forum() {
   $this->db = &$GLOBALS['db'];
  }
  
  function get_entry($action='',$depth=0,$id=false) {
   $this->load();
   if ($id === false) {
    if (!isset($_GET['id'])) redirect('./');
    $id = (int) $_GET['id'];
   } 
   if ($id == 0) {
    $entry = $this->create_root($action,$depth);
   }
   else {
    $entry = &$this->entries[$id];
    if (!is_object($entry)) redirect('./');
    $entry = $this->history($entry);
    $entry->user_rights();
    if (!$entry->right_to($action)) redirect('./');
   }
   $page = 1;
   if (isset($_GET['page'])) {
    $page = $_GET['page'];
   }
   $entry = $this->index($this->entries,$entry,$action,$depth,$page);
   $entry = $this->index($entry,$action,$depth,$page);
   return $entry;
  }

  function load() {return 0;
   $this->set_query();
   $this->db->query();
   while ($row = mysql_fetch_array($this->db->result)) {
    $entry = &$this->entries[$row['id']];
    if (isset($entry)) {
     $entry->add_rights($row);
    }
    else {
     $entry = new entry($row);
    }
   }
  }
  
  function set_query() {
   $this->db->query = 'select
                    forum.id,
                    forum.author,
                    forum.created,
                    forum.topic,
                    forum.text,
                    forum_relation.entry,
                    forum_relation.answer,
                    person.first_name as author_first_name,
                    person.last_name as author_last_name,
                    rights.type as rights_type,
                    rights.type_id as rights_type_id,
                    rights.rights
                   from forum
                    left join person on forum.author=person.id
                    left join rights on rights.ref="forum" and rights.ref_id=forum.id
                   order by forum.rel_to, forum.created, forum.id, rights.type';
//		   die($this->db->query);
  }
  
  function create_root($depth=0) {
   $entry = new entry();
   $answers = array();
   foreach ($this->entries as $id => $answer) {
    $answer->admin = $answer->data['author'];
    $answer->user_rights();
    if ($answer->right_read()) {
     $answers[$id] = $answer;
    }
   }
   $entry->answers = $answers;
   return $entry;
  }
  
  function history($entry) {
   $entry->admin = $entry->data['author'];
   $prev = &$this->entries[$entry->data['rel_to']];
   while (isset($prev)) {
    $entry->admin = $prev->data['author'];
    $entry->rights+= $prev->rights;
    $prev = &$this->entries[$prev->data['rel_to']];
   }
   return $entry;
  }
  
  function index($entry,$action,$depth,$page=0) {
   $answers = array();
   $entry->depth = $depth;
   $pages = 0;
   $num_answers = 0;
   $max_num_answers = 10;
   $query = 'select id from forum where rel_to="'.$entry->data['id'].'"';
   $result = $this->db->query($query);
   while ($row = mysql_fetch_row($result)) {
    $answer_id = $row[0];
    $answer = $this->entries[$answer_id];
    $answer->admin = $entry->admin;
    $answer->rights+= $entry->rights;
    $answer->user_rights();
    if ($answer->right_to($action)) {
     $num_answers++;
     if ($num_answers > $max_num_answers) {
      $pages++;
      $num_answers = 1;
     }
     if ($page == $pages || !$page) {
      if ($depth != 0) {
       $answer = $this->index($answer,$action,$depth-1);
      }
      $answers[] = $answer;
     }
    }
   }
   $entry->answers = $answers;
   $entry->pages = $pages;
   return $entry;
  }
  
  function index_old($entries,$entry,$action,$depth,$page=0) {
   $entry_cache = $entries;
   $answers = array();
   $entry->depth = $depth;
   $pages = 0;
   $num_answers = 0;
   $max_num_answers = 10;
   $found = false;
   foreach ($entry_cache as $id => $answer) {
    if ($answer->data['rel_to'] == $entry->data['id']) {
     $found = true;
     unset($entries[$id]);
     $answer->admin = $entry->admin;
     $answer->rights+= $entry->rights;
     $answer->user_rights();
     if ($answer->right_to($action)) {
      $num_answers++;
      if ($num_answers > $max_num_answers) {
       $pages++;
       $num_answers = 1;
      }
      if ($page == $pages || !$page) {
       if ($depth != 0) {
        $answer = $this->index(&$entries,$answer,$action,$depth-1);
       }
       $answers[] = $answer;
      }
     }
    }
    else {
     if ($found == true) {
      break;
     }
    }
   }
   $entry->answers = $answers;
   $entry->pages = $pages;
   return $entry;
  }
  
  function count_new_entries() {
   $new_entries = array();
   foreach ($this->entries as $id => $entry) {
    $datetime = $entry->data['created'];
    if (dt_is_new($datetime)) {
     $new_entries[] = $id;
    }
   }
   return count($new_entries);
  }

 }

?>
