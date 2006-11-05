<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 
 /*
  * An entry with given data.
  */
 class sub_entry {
  var $id = 0;
  var $db;
  var $data;			// array of properties
  var $new = false;		// Is this a new entry?
  var $history = array();	// array of parents
  var $entries = array();	// all needed entries
  var $answers = array();	// answers to this entry
  var $levels = array();	// all answers sorted by their level to this entry
  var $read = array();		// already read entries
  var $new_answers = 0;		// number of new answers
  var $depth = 0;		// depth/level related to the main entry
  var $admin;			// person id of the admin
  var $rights = array();	// person rights and group rights
  var $user_rights;		// bit coded rights of the user
  var $fdata;			// formatted data string or template

  function sub_entry() {
   $this->db = &$GLOBALS['db'];
  }
  
  /*
   * can be overwritten
   */
  function new_entry() {
   return new sub_entry();
  }

  function set_data($data) {
   $this->data = $data;
   $this->id = &$this->data['id'];
  }
  
  /*
   * adds one data array (db row)
   * to the right array
   */
  function add_rights_row($data) {
   if (isset($data['rights_person'])) {
    $this->rights['person'] = $data['rights_person'];
   }
   else {
    if (isset($data['rights_group'])) {
     if (isset($data['gid'])) {
      $gid = $data['gid'];
      $this->rights['group'][$gid] = $data ['rights_group'];
     }
    }
   }
  }
  
  /*
   * used to adopt rights of parent
   */
  function add_rights($rights) {
   if (!is_array($rights)) return;
   $this->rights += $rights;
  }
  
  /*
   * creates a new answer (as object)
   * and inserts it into the database
   */
  function new_answer($data) {
   $answer = new sub_entry();
   $answer->set_data($data);
   $answer->history = $this->history;
   $answer->history[0] = &$this;
   $answer->insert();
   return $answer;
  }

  /*
   * database interaction
   */
  function mark_read() {
   if (!$this->new) return;
   $query = 'forum_read (person_id, entry_id, created)
             values ("'.$_SESSION['userid'].'","'.$this->id.'","'.$this->data['created'].'")';
   $this->db->insert($query);
  }
  
  function insert($data=null) {
   if (isset($data)) {
    $this->data = $data;
   }
   $query = 'forum (rel_to,author,created,topic,text) values';
   $query.= '("'.$this->data['rel_to'].'","'.$_SESSION['userid'].'",now(),"'.$this->data['topic'].'","'.$this->data['text'].'")';
   $this->db->insert($query);
   $this->id = $this->db->insert_id;
   foreach ($this->history as $level => $parent) {
    $query = 'forum_relation
               (entry,answer,level) 
              values
               (
                "'.$parent->id.'",
                "'.$this->id.'",
                "'.($level+1).'"
               )';
    $this->db->insert($query);
   }
  }

  function update($new_data) {
   $data = &$this->data;
   $data['topic'] = $new_data['topic'];
   $data['text'] = $new_data['text'];
   $v = $data;
   $v['datetime'] = local_date(datetime(),'yyyy_mm_dd_hh_ii_ss');
   $tmpl = new tmpl('edit_signature.html',$v);
   $data['text'] = $tmpl->fdata;
   $id = $data['id'];
   $topic = $data['topic'];
   $text = $data['text'];
   $query = "forum set
                   topic='$topic',
                   text='$text'
                  where id='$id'";
   $this->db->update($query);
  }

  function update_rights($data) {
   if (isset($data['person'])) {
    foreach ($data['person'] as $id => $right_array) {
     $rights = implode_rights($right_array);
     $query = 'forum_rights_person set rights="'.$rights.'" where entry_id="'.$this->data['id'].'" and id="'.$id.'"';
     $this->db->update($query);
    }
   }
   if (isset($data['group'])) { 
    foreach ($data['group'] as $id => $right_array) {
     $rights = implode_rights($right_array);
     $query = 'forum_rights_group set rights="'.$rights.'" where entry_id="'.$this->data['id'].'" and id="'.$id.'"';
     $this->db->update($query);
    }
   }
  }

  function create_rights($data) {
   $person_id = 0;
   $group_id = 0;
   $person_name = $data['person']['name'];
   if ($person_name) {
    $id_name = find_name('person',$person_name,false,'exact');
    if (count($id_name) == 1) {
     list($person_id,$person_name) = each($id_name);
    }
   }
   $group_name = $data['group']['name'];
   if ($group_name) {
    $id_name = find_name('group',$group_name,false,'exact');
    if (count($id_name) == 1) {
     list($group_id,$group_name) = each($id_name);
    }
   }
   if ($person_id) {
    $query = 'forum_rights_person (
               entry_id, 
               person_id, 
               rights
              )
              values (
               "'.$this->id.'",
               "'.$person_id.'",
               "'.implode_rights($data['person']).'"
              )';
    $this->db->insert($query);      
   }
   if ($group_id) {
    $query = 'forum_rights_group (
               entry_id, 
               group_id, 
               rights
              )
              values (
               "'.$this->id.'",
               "'.$group_id.'",
               "'.implode_rights($data['group']).'"
              )';
    $this->db->insert($query);      
   }
  }
  
  function del_right($id,$type) {
   $query = 'forum_rights_'.$type.' where id="'.$id.'"';
   $this->db->delete($query);
  }
  
  
  /*
   * right evaluation
   */
  function user_rights() {
   $rights = 0;
   if (isset($this->rights['person'])) {
    $rights = $this->rights['person'];
   }
   else {
    if (isset($this->rights['group'])) {
     foreach ($this->rights['group'] as $gid => $group_rights) {
      $rights|= $group_rights;
     }
    }
   }
   if ($this->admin == $_SESSION['userid']) {
    $rights|= 129;
   }
   $this->user_rights = $rights;
   return $this->user_rights;
  }

  function right_to($do) {
   if (!$do) return true;
   $func = 'right_'.$do;
   $allowed = $this->$func();
   return $allowed;
  }

  function right_read() {
   $allowed = false;
   if (($this->user_rights & 1) == 1) $allowed = true;
   return $allowed;
  }

  function right_answer() {
   $allowed = false;
   if (($this->user_rights & 2) == 2) $allowed = true;
   return $allowed;
  }

  function right_edit() {
   $allowed = false;
   $uid = $_SESSION['userid'];
   if (($this->user_rights & 4) == 4 && $this->data['author'] == $uid) $allowed = true;
   elseif (($this->user_rights & 32) == 32) $allowed = true;
   $allowed&= $this->right_read();
   return $allowed;
  }

  function right_delete() {
   $allowed = false;
   $uid = $_SESSION['userid'];
   if (($this->user_rights & 8) == 8 && $this->data['author'] == $uid) $allowed = true;
   elseif (($this->user_rights & 64) == 64) $allowed = true;
   $allowed&= $this->right_read();
   return $allowed;
  }

  function right_rights() {
   $allowed = false;
   $uid = $_SESSION['userid'];
   if (($this->user_rights & 16) == 16 && $this->data['author'] == $uid) $allowed = true;
   elseif (($this->user_rights & 128) == 128) $allowed = true;
   $allowed&= $this->right_read();
   return $allowed;
  }
  
  
  /*
   * format functions
   */
  function format($tmpl='entry.html',$level=0,$levelinfo=array()) {
   $this->format_data();
   $this->format_number_answers();
   $this->format_level($level,$levelinfo);
   $this->format_user_rights();
   $this->format_history();
   $fdata = $this->tmpl($tmpl);
   if ($tmpl == 'entry.html') {
    $this->mark_read();
   }
   return $fdata;
  }

  function format_data() {
   $fdata = $this->data;
   if ($fdata['topic'] == '') $fdata['topic'] = '?';
   $fdata['topic_html'] = mask_html($fdata['topic']);
   $fdata['text_html'] = mask_html($fdata['text']);
   $fdata['text_html_xt'] = html_br($fdata['text']);
   if ($this->new)  {
    $fdata['created_html'] = local_date_new($fdata['created']);
   } 
   else {
    $fdata['created_html'] = local_date($fdata['created'],'ymdHi');
   }
   $this->fdata = $fdata;
  }

  function format_number_answers() {
   $num = count($this->answers);
   $this->fdata['num_ans'] = $num;
   $this->fdata['zero_answers'] = array();
   $this->fdata['existing_answers'] = array();
   if ($num) {
    $this->fdata['existing_answers'][] = array();
   }
   else {
    $this->fdata['zero_answers'][] = array();
   }
  }

  function format_level($level,$levelinfo) {
   $spacer = array();
   for ($i=1;$i<=$level;$i++) {
    $spacer[$i]['i'] =$i%2;
    $info = 0;
    if (isset($levelinfo[$i-1])) {
     $info = $levelinfo[$i-1];
    }
    $spacer[$i]['spacer_type0'] = array();
    $spacer[$i]['spacer_type1'] = array();
    $spacer[$i]['spacer_type2'] = array();
    $spacer[$i]['spacer_type3'] = array();
    if ($info == 0) $type = 'spacer_type0';
    if ($info >= 1) $type = 'spacer_type1';
    if ($i == $level) $type = 'spacer_type2';
    if ($i == $level && $info > 1) $type = 'spacer_type3';
    $spacer[$i][$type][] = array();
   }
   $this->fdata['spacer'] = $spacer;
  }

  function format_user_rights() {
   $answer = array();
   $edit = array();
   $delete = array();
   $rights = array();
   if ($this->right_answer()) $answer[] = array();
   if ($this->right_edit()) $edit[] = array();
   if ($this->right_delete()) $delete[] = array();
   if ($this->right_rights()) $rights[] = array();
   $this->fdata['answer'] = $answer;
   $this->fdata['edit'] = $edit;
   $this->fdata['delete'] = $delete;
   $this->fdata['rights'] = $rights;
  }
  
  function format_history() {
   $history = array();
   for ($i=count($this->history)-1;$i>0;$i--) {
    $parent = $this->history[$i];
    $parent_data = array(
     'parent_id' => $parent->id,
     'parent_topic' => $parent->data['topic'],
    );
    $history[$i] = $parent_data;
   }
   $this->fdata['history'] = $history;
  }
  
  function tmpl($file) {
   $tmpl = new tmpl($file,$this->fdata);
   return $tmpl->fdata;
  }
  
 }
 
 class entry extends sub_entry {
 
  function get() {
   if (isset($_GET['id'])) {
    $this->id = $_GET['id'];
   }
  }
  
  function load() {
//   $this->id = $id;
   if ($this->load_data()) {
    $this->load_history();
    $this->user_rights();
    $this->load_read();
   } 
//   $this->load_answers($depth);
  }
  
  function load_data() {
   if ($this->id) {
    $pid = $_SESSION['userid'];
    $query = 'select
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
              from forum 
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
               forum.id="'.$this->id.'"';
    $this->db->query($query);
    $data = mysql_fetch_array($this->db->result);
    $this->set_data($data);
    $this->add_rights_row($data);
    while ($right_data = mysql_fetch_array($this->db->result)) {
     $this->add_rights_row($right_data);
    }
    return mysql_num_rows($this->db->result);
   } else {
    $data = array(
     'id' => 0,
     'rel_to' => 0,
     'author' => 0,
     'created' => '',
     'topic' => '',
     'text' => ''
    );
    $this->set_data($data);
   }
  }

  function load_history() {
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
              forum_relation.entry=forum.id 
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
              forum_relation.answer="'.$this->id.'" 
             order by forum_relation.level';
   $this->db->query($query);
   while ($data = mysql_fetch_array($this->db->result)) {
    $level = $data['level'];
    $id = $data['id'];
    $parent = &$this->entries[$id];
    $this->history[$level] = &$parent;
    if (!isset($parent)) {
     $parent = new sub_entry();
     $parent->set_data($data);
    }
    $parent->add_rights_row($data);
   }
   $this->admin = $this->data['author'];
   for ($i=1;$i<count($this->history);$i++){
    $parent = &$this->history[$i];
    $this->admin = $parent->data['author'];
    $this->add_rights($parent->rights);
   }  
  }
  
  function check_right($right) {
   if (!$this->right_to($right)) {
    redirect('./');
   }
  }

  function load_answers($depth=1,$right='') {
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
              forum_relation.entry="'.$this->id.'"';
   if (isset($depth)) {
    $query.=  'and level<="'.$depth.'"';
   }
   $query.= 'order by
              forum_relation.level,
              forum.created';
   $result = $this->db->query($query);
   while ($data = mysql_fetch_array($result)) {
    $level = $data['level'];
    $id = $data['id'];
    $answer = &$this->entries[$id];
    if (!isset($answer)) {
     $answer = $this->new_entry();
     $answer->set_data($data);
     $answer->depth = $depth - $level;
     $this->levels[$level][$id] = &$answer;
     if ($data['created'] > $_SESSION['last_login']) {
      if (!isset($this->read[$answer->id])) {
       $answer->new = true;
      } 
     }
    }
    $answer->add_rights_row($data);
   }
   $this->index();
  }
  
  function load_read() {
   $query = 'select entry_id from forum_read where person_id="'.$_SESSION['userid'].'"';
   $result = $this->db->query($query);
   while (list($entry_id) = mysql_fetch_row($result)) {
    $this->read[$entry_id] = true;
   }
   if ($this->id && $this->data['created'] > $_SESSION['last_login'] && !isset($this->read[$this->id])) {
    $this->new = true;
   }
  }

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
     $entry = new sub_entry();
     $entry->set_data($data);
     if (!isset($this->read[$id])) {
      $entry->new = true;
     } 
    }
    $entry->add_rights_row($data);
   }
   $this->index_new();
  }
  
  function index($right='read') {
   $this->entries[$this->id] = &$this;
   for ($i=1;$i<=count($this->levels);$i++) {
    foreach ($this->levels[$i] as $id => $entry) {
     $answer = &$this->entries[$id];
     $parent_id = $answer->data['rel_to'];
     $parent = &$this->entries[$parent_id];
     if ($parent->id == 0) {
      $answer->admin = $answer->data['author'];
     }
     else {
      $answer->admin = &$parent->admin;
     }
     $answer->add_rights($parent->rights);
     $answer->user_rights();
     if ($answer->right_to($right)) {
      $parent->answers[$id] = &$answer;
     }
    }
   }
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
