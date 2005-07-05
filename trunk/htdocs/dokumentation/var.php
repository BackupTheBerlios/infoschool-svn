<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */
 if (!isset($root)) $root = './';
 include 'func.php';
 $root.= '../';
 include $root.'var.php';

 $output->title[] = 'documentation';

 $dok_menu['Einleitung'] = 'einleitung.html';
 $dok_menu['Startseite'] = 'startseite.html';
 $dok_menu['Kalender'] = array(
  '0' => 'kalender/index.html',
  'Termin' => 'kalender/todo.html',
  'Todoliste' => 'kalender/todoliste.html',
  'Vertretungsplan' => 'kalender/vplan.html',
 );
 $dok_menu['Foren'] = array(
  '0' => 'foren/index.html',
  'Rechte' => 'foren/rechte.html',
  'Verwalten' => 'foren/verwalten.html',
 );
 $dok_menu['Dateiaustausch'] = array(
  '0' => 'dateien/index.html',
  'Rechte' => 'dateien/rechte.html',
  'Verwalten' => 'dateien/verwalten.html',
 );
 $dok_menu['Messages'] = array(
  '0' => 'messages/index.html',
  'Neue Message' => 'messages/neu.html',
 );
 $dok_menu['Zensuren'] = 'zensuren/index.html';
 $dok_menu['Benutzer / Gruppen'] = array(
  '0' => 'benutzer/index.html',
  'Person' => 'benutzer/person.html',
  'Gruppe' => 'benutzer/gruppe.html',
  'Gruppe Verwalten' => 'benutzer/gruppe_verwalten.html',
 );
 $dok_menu['News'] = array(
  '0' => 'news/index.html',
  'Neue News' => 'news/neu.html',
 );

?>