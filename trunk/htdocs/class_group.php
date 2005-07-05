<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */

 class group {
  var $db;
  var $data = array();
  var $id;
  var $meetings = array();
  var $members = array();
  var $fdata = array();

  function group($id=0) {
   $this->db = &$GLOBALS['db'];
   $this->id = &$this->data['id'];
   if ($id) $this->load($id);
  }

  function create($name) {
   if ($name == '') $name = $_SESSION['name'];
   $insert = 'gruppe (name,leiter) values ("'.$name.'","'.$_SESSION['userid'].'")';
   $this->db->insert($insert);
  }

  function delete() {
   $this->delete_meetings();
   $this->delete_members();
   $this->delete_rights();
   $this->db->delete('gruppe where id="'.$this->id.'"');
  }

  function delete_meetings() {
   foreach ($this->meetings as $i => $meeting) {
    $this->del_meeting($meeting['id']);
   }
  }

  function delete_members() {
   $this->db->delete('pg where gid="'.$this->id.'"');
   $this->db->delete('neu_pg where gid="'.$this->id.'"');
  }

  function delete_rights() {
   $this->db->delete('forum_rights_group where group_id="'.$this->id.'"');
   $this->db->delete('dateien_recht_gruppe where gruppe_id="'.$this->id.'"');
   $this->db->delete('news_level_gruppe where gruppe_id="'.$this->id.'"');
  }

  function load($id=0) {
   if (!$id) $id = $this->data['id'];
   $select = 'gruppe.id, gruppe.name, gruppe.dsc as description, gruppe.leiter as head_id, gruppe.notiz as note';
   $select.= ', person.first_name as head_first_name, person.last_name as head_last_name';
   $select.= ' from gruppe left join person on gruppe.leiter=person.id where gruppe.id="'.$id.'"';
   $this->db->select($select);
   if ($this->db->num_rows == 1) {
    $this->data = $this->db->data[0];
    $this->load_meetings($id);
    $this->load_members($id);
    return true;
   }
  }

  function load_meetings($gid) {
   $select = 'id, day as dow, time from lesson where gid="'.$gid.'"';
   $this->db->select($select);
   $meetings = $this->db->data;
   foreach($meetings as $i => $m) {
    $select = 'datum, status from vertretung where sid="'.$m['id'].'"';
    $this->db->select($select);
    $status = $this->db->data;
    foreach($status as $ii => $s) {
     $status[$ii]['date'] = tmpl_date_title($s['datum']);
    }
    $meetings[$i]['status'] = $status;
   }
   $this->meetings = $meetings;
  }

  function load_members($gid) {
   $select = 'person.id as member_id, person.first_name, person.last_name from pg,person';
   $select.= ' where pg.gid="'.$gid.'" and pg.pid=person.id order by person.last_name, person.first_name';
   $this->db->select($select);
   $this->members = $this->db->data;
  }

  function save($data) {
   $id = $this->data['id'];
   $name = text2html($data['name']);
   $dsc = text2html($data['description']);
   $notiz = text2html($data['note']);
   $update = "gruppe set
                     name='$name',
                     dsc='$dsc',
                     notiz='$notiz'
                    where id='$id'";
   $this->db->update($update);
  }

  function add_member($pid=0) {
   if (!$pid) $pid = $_SESSION['userid'];
   $gid = $this->data['id'];
   $insert = 'pg (pid,gid) values("'.$pid.'","'.$gid.'")';
   $this->db->insert($insert);
  }

  function del_member($pid=0) {
   if (!$pid) $pid = $_SESSION['userid'];
   $gid = $this->data['id'];
   $delete = 'pg where gid="'.$gid.'" and pid="'.$pid.'"';
   $this->db->delete($delete);
  }

  function is_member($uid=0) {
   if (!$uid) $uid = $_SESSION['userid'];
   foreach ($this->members as $i => $m) {
    if ($m['member_id'] == $uid) return true;
   }
   return false;
  }

  function is_member_request() {
   $pid = $_SESSION['userid'];
   $gid = $this->data['id'];
   $query = '1 from neu_pg where pid="'.$pid.'" and gid="'.$gid.'"';
   $this->db->select($query);
   return $this->db->num_rows;
  }

  function member_request() {
   $pid = $_SESSION['userid'];
   $gid = $this->data['id'];
   $insert = 'neu_pg (pid,gid) values ("'.$pid.'","'.$gid.'")';
   $this->db->insert($insert);
  }

  function del_member_request($pid) {
   $gid = $this->data['id'];
   $query = 'neu_pg where pid="'.$pid.'" and gid="'.$gid.'"';
   $this->db->delete($query);
  }

  function is_head() {
   if ($this->data['head_id'] == $_SESSION['userid']) return true;
   else return false;
  }

  function add_meeting($m) {
   $query = 'lesson (gid,day,time) values("'.$this->id.'","'.$m['dow'].'","'.$m['time'].'")';
   $this->db->insert($query);
  }

  function del_meeting($id) {
   $query = 'lesson where id="'.$id.'" and gid="'.$this->id.'"';
   $this->db->delete($query);
   if ($this->db->affected_rows == 1) {
    $query = 'vertretung where sid="'.$id.'"';
    $this->db->delete($query);
   }
  }

  function mod_meetings($m) {
   foreach ($m as $id => $data) {
    $query = 'lesson set day="'.$data['dow'].'", time="'.$data['time'].'" where id="'.$id.'" and gid="'.$this->id.'"';
    $this->db->update($query);
   }
  }


  function format() {
   $fdata = $this->data;
   if (!isset($fdata['description'])) $fdata['description'] = '';
   if (!isset($fdata['note'])) $fdata['note'] = '';
   $meetings = array();
   foreach ($this->meetings as $mid => $meeting) {
    $meeting['mid'] = $meeting['id'];
    for ($i=0;$i<7;$i++) {
     $meeting['dow'.$i] = array();
     if ($i == $meeting['dow']) {
      $meeting['dow'.$i][] = array();
     }
    }
    $meetings[$mid] = $meeting;
   }
   $fdata['meetings'] = array();
   $fdata['meetings_number'] = count($meetings);
   if (count($meetings) > 0) {
    $fdata['meetings'][]['meeting'] = $meetings;
   }
   $fdata['members_number'] = count($this->members);
   $fdata['member'] = $this->members;
   $fdata['pg_action'] = 'join group';
   if ($this->is_member()) $fdata['pg_action'] = 'leave group';
   if ($this->is_member_request()) $fdata['pg_action'] = 'remove request';
   $this->fdata = $fdata;
  }
 }
?>
