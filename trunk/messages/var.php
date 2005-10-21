<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'class.php';
 include 'func.php';
 if(!isset($root)) $root = '';
 $root.= '../';
 include $root.'var.php';
 
 $output->title[] = 'Messages';
 $output->menu['messages/'] = array(
  './' => 'Messages',
  'index.php' => 'Received',
  'sent.php' => 'Sent',
//  './?trash=1' => 'Trash',
  'new.php' => 'new message',
 );
 
 $table_messages = 'messages';
 $table_messages_recipients = 'messages_to';
?>