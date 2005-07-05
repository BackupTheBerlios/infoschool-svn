<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 function person_requests() {
  global $db;
  $db->select('id, vorname as first_name, nachname as last_name from neu_account');
  $data = array();
  foreach ($db->data as $i => $person) {
   $person['first_name'] = mask_html($person['first_name']);
   $person['last_name'] = mask_html($person['last_name']);
   $data[] = $person;
  }
  return $data;
 }

 function person_requests_mod($persons) {
  global $db;
  foreach ($persons as $id => $decision) {
   if ($decision) {
    $db->select('vorname as first_name, nachname as last_name, passwd, mail from neu_account where id="'.$id.'"');
    if ($db->num_rows == 1) {
     $person = $db->data[0];
    } else $person['mail'] = '';
    if ($decision == 2) {
     $query = 'person (first_name, last_name, passwd, mail) values ';
     $query.= '("'.$person['first_name'].'","'.$person['last_name'].'","'.$person['passwd'].'","'.$person['mail'].'")';
     $db->insert($query);
    }
    $to = $person['first_name'].' '.$person['last_name'];
    $tmpl = new tmpl('mail_person_new_subject.txt');
    $subject = $tmpl->fdata;
    $v['decision'] = $decision;
    $tmpl = new tmpl('mail_person_new.txt',$v);
    $data = $tmpl->fdata;
    new mail($person['mail'],$to,$subject,$data,false,true);
    $db->delete('neu_account where id="'.$id.'"');
   }
  }
 }

 $output->secure('admin');

 if (isset($_POST['persons'])) {
  person_requests_mod((array) $_POST['persons']);
 }

 $v['person'] = person_requests();

 $content = new tmpl('person_new.html',$v);

 $output->out($content);
?>