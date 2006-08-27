<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 
 /*
  * represents a directory or file
  */
 class fs_item {
  var $data = array();
  var $upper_dir = null;
  var $admin = 0;
  var $right_arr = array();
  var $items = array();
  var $user_rights = 0; // binary coded user rights
  
  function fs_item($id=null) {
   if (isset($id)) {
    $this->load($id);
   } 
  }
  
  function load($id) {
   if ($id) {
    global $db;
    $query = 'fs.id, fs.rel_to, fs.filetype, fs.owner, fs.last_change, fs.name, fs_person.rights person_rights, pg.gid, fs_group.rights group_rights
    		from filesystem as fs 
    		left join filesystem_rights_person as fs_person on
    		 fs.id="'.$id.'" and
    		 fs.id=fs_person.fs_id and
    		 fs_person.person_id="'.$_SESSION['userid'].'"
    		left join pg on
    		 fs_person.id is null and
    		 pg.pid="'.$_SESSION['userid'].'"
    		left join filesystem_rights_group as fs_group on
    		 pg.gid is not null and
    		 pg.gid=fs_group.group_id and
    		 fs.id=fs_group.fs_id
    		where fs.id="'.$id.'"';
    $db->select($query);
    $this->create_data($db->data);
    $this->load_upper_dir();
    $this->merge_right_arr();
   }
   else {
    $this->create_root();
   }
   $this->user_rights();
  }
  
  function create_data($d) {
   if (count($d) == 0) {
    $this->create_root();
    return;
   } 
   $this->data = $d[0];
   $right_arr = array();
   if (isset($this->data['person_rights'])) {
    $right_arr['person'] = $this->data['person_rights'];
   }
   if (isset($this->data['group_rights'])) {
    foreach ($d as $i => $r) {
     $group_id = $r['gid'];
     $right_arr['group'][$group_id] = $r['group_rights'];
    }
   }
   $this->right_arr = $right_arr;
  }
  
  function create_root() {
   $data = array(
    'id' => 0,
    'rel_to' => 0,
    'filetype' => '',
    'owner' => 0,
    'last_change' => 0,
    'name' => '{file exchange}',
   );
   $this->data = $data;
   $this->user_rights = (1 | 2);
  }
  
  function load_upper_dir() {
   $upper_id = $this->data['rel_to'];
   $this->upper_dir = new fs_item($upper_id);
  }
  
  function merge_right_arr() {
   if ($this->data['id']) {
    $this->upper_dir->merge_right_arr();
    $this->merge_upper_rights();
   }
  }
  
  function merge_upper_rights() {
   $this->right_arr = array_overwrite($this->upper_dir->right_arr,$this->right_arr);
   if ($this->data['rel_to']) {
    $this->admin = $this->upper_dir->admin;
   }
   else {
    $this->admin = $this->data['owner'];
   }
  }
  
  function user_rights() {
   $r = $this->user_rights;
   if (isset($this->right_arr['person'])) {
    $r = $this->right_arr['person'];
   }
   elseif (isset($this->right_arr['group'])) {
    foreach ($this->right_arr['group'] as $group_id => $group_rights) {
     $r|= $group_rights;
    }
   }
   if ($this->admin == $_SESSION['userid']) {
    $r|= 129;
   }
   $this->user_rights = $r;
   return $r;
  }
  
  function load_items() {
   global $db;
   $rel_to = $this->data['id'];
   $query = '	fs.id,
   		fs.rel_to, 
   		fs.filetype,
   		fs.owner,
   		fs.last_change, 
   		fs.name, 
   		person.first_name owner_first_name,
   		person.last_name owner_last_name,
   		fs_person.rights person_rights, 
   		pg.gid, 
   		fs_group.rights group_rights
   		from filesystem as fs 
   		left join person on
   		 fs.owner=person.id
   		left join filesystem_rights_person as fs_person on
   		 fs.rel_to="'.$rel_to.'" and
   		 fs.id=fs_person.fs_id and
   		 fs_person.person_id="'.$_SESSION['userid'].'"
   		left join pg on
   		 fs_person.id is null and
   		 pg.pid="'.$_SESSION['userid'].'"
   		left join filesystem_rights_group as fs_group on
   		 pg.gid is not null and
   		 pg.gid=fs_group.group_id and
   		 fs.id=fs_group.fs_id
   		where fs.rel_to="'.$rel_to.'"';
   $db->select($query);
   $items_data = $db->data;
   $item_arrays = array();
   foreach ($items_data as $i => $item_data_part) {
    $item_id = $item_data_part['id'];
    if (!isset($item_arrays[$item_id])) {
     $item_arrays[$item_id] = array();
    }
    $item_arrays[$item_id][] = $item_data_part;
   }
   $items = array();
   foreach ($item_arrays as $i => $item_data) {
    $item = new fs_item();
    $item->upper_dir = &$this;
    $item->create_data($item_data);
    $item->merge_upper_rights();
    $item->user_rights();
    $items[] = $item;
   }
   $this->items = $items;
  }
  
  function right_read() {
   $allowed = false;
   if (($this->user_rights & 1) == 1) $allowed = true;
   return $allowed;
  }
  
  function right_upload() {
   $allowed = false;
   if (($this->user_rights & 2) == 2) $allowed = true;
   return $allowed;
  }

  function right_rename() {
   $allowed = false;
   $uid = $_SESSION['userid'];
   if (($this->user_rights & 4) == 4 && $this->data['owner'] == $uid) $allowed = true;
   elseif (($this->user_rights & 32) == 32) $allowed = true;
   $allowed&= $this->right_read();
   return $allowed;
  }

  function right_delete() {
   $allowed = false;
   $uid = $_SESSION['userid'];
   if (($this->user_rights & 8) == 8 && $this->data['owner'] == $uid) $allowed = true;
   elseif (($this->user_rights & 64) == 64) $allowed = true;
   $allowed&= $this->right_read();
   return $allowed;
  }

  function right_rights() {
   $allowed = false;
   $uid = $_SESSION['userid'];
   if (($this->user_rights & 16) == 16 && $this->data['owner'] == $uid) $allowed = true;
   elseif (($this->user_rights & 128) == 128) $allowed = true;
   $allowed&= $this->right_read();
   return $allowed;
  }
  
  function format() {
   $this->format_data();
   $this->format_upper_dir();
   $items = $this->format_items();
   $tmpl = new tmpl('index.html',$this->data);
   return $tmpl->fdata;
  }
  
  function format_data($prefix='') {
   $this->format_user_rights();
   $d = &$this->data;
   $d['dir'] = array();
   $d['file'] = array();
   if (isset($d['filetype'])) $d['file'][] = array();
   else $d['dir'][] = array();
   $d['last_change_local'] = local_date($d['last_change']);
   $d['name_html'] = mask_html($d['name']);
   if ($prefix) {
    $d_cache = $d;
    foreach ($d_cache as $key => $value) {
     $d[$prefix.$key] = $value;
    }
   }
  }
  
  function format_user_rights() {
   $d = &$this->data;
   $d['user_rights'] = $this->user_rights;
   $d['upload'] = array();
   $d['rights'] = array();
   if ($this->right_upload()) $d['upload'][] = array();
   if ($this->right_rights()) $d['rights'][] = array();
  }
  
  function format_upper_dir() {
   $upper = array();
   if ($this->data['id']) {
    $this->upper_dir->format_data();
    $upper = $this->upper_dir->format_upper_dir();
   }
   $upper[] = array(
    'upper_dir_id' => $this->data['id'],
    'upper_dir_name' => $this->data['name_html'],
   );
   $this->data['upper_dir'] = $upper;
   return $upper;
  }
  
  function format_items() {
   $items = array();
   foreach ($this->items as $i => $item) {
    if ($item->right_read()) {
     $item->format_data('item_');
     $items[] = $item->data;
    }
   }
   $this->data['item'] = $items;
  }
 
  function update_rights($data) {
   global $db;
   if (isset($data['person'])) {
    foreach ($data['person'] as $id => $right_array) {
     $rights = implode_rights($right_array);
     $query = 'filesystem_rights_person set rights="'.$rights.'" where fs_id="'.$this->data['id'].'" and id="'.$id.'"';
     $db->update($query);
    }
   }
   if (isset($data['group'])) { 
    foreach ($data['group'] as $id => $right_array) {
     $rights = implode_rights($right_array);
     $query = 'filesystem_rights_group set rights="'.$rights.'" where fs_id="'.$this->data['id'].'" and id="'.$id.'"';
     $db->update($query);
    }
   }
  }

  function create_rights($data) {
   global $db;
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
    $query = 'filesystem_rights_person (
               fs_id, 
               person_id, 
               rights
              )
              values (
               "'.$this->data['id'].'",
               "'.$person_id.'",
               "'.implode_rights($data['person']).'"
              )';
    $db->insert($query);      
   }
   if ($group_id) {
    $query = 'filesystem_rights_group (
               fs_id, 
               group_id, 
               rights
              )
              values (
               "'.$this->data['id'].'",
               "'.$group_id.'",
               "'.implode_rights($data['group']).'"
              )';
    $db->insert($query);      
   }
  }
 
  function del_right($id,$type) {
   global $db;
   $query = 'filesystem_rights_'.$type.' where id="'.$id.'"';
   $db->delete($query);
  }
  
  function mkdir($name) {
   if ($name == '') $name = '{Directory}';
   global $db;
   $query = 'filesystem (rel_to, owner, last_change, name) values (
   		"'.$this->data['id'].'",
   		"'.$_SESSION['userid'].'",
   		now(),
   		"'.$name.'")';
   $db->insert($query);
  }
 
  /* file_arr has these informations
   * 'tmp_name': whole path to the temporary file
   * 	in the server's filesystem, deleted at end of script
   * 'name': filename from the client
   * 'size': filesize in bytes
   * 'type': mime type send by client
   */
  function insert_file($file_arr) {
   $type = $file_arr['type'];
   $data = file_data($file_arr['tmp_name']);
   $data = addslashes($data);	// for mysql-insertion
   global $db;
   $query = 'filesystem (rel_to, owner, last_change, name, filetype, data) values (
   		"'.$this->data['id'].'",
   		"'.$_SESSION['userid'].'",
   		now(),
   		"'.$file_arr['name'].'",
   		"'.$type.'",
   		"'.$data.'")';
   $db->insert($query);
  }
  
  function send() {
   //header('Content-Type: application/octet-stream'); // possibly force an download
   header('Content-Type: '.$this->data['filetype']);
   header('Content-Disposition: attachment; filename='.$this->data['name']);
   global $db;
   $query = 'data from filesystem where id="'.$this->data['id'].'"';
   $db->select($query);
   echo $db->data[0][0];
  }

 }
?>
