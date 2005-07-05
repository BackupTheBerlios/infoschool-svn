<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 *
 * Allgemeine Variablen Datei
 * Fast alle Skripte binden diese Datei ein.
 * Hier werden Variablen definiert, die im Hauptverzeichnis
 * oder in mehreren Unterverzeichnissen gebraucht werden.
 */

 // point at the directory htdocs
 if(!isset($root)) $root = './';

 // Einbinden der Klassen- und Funktionendatei
 // Falls diese Datei aus einem anderen Verzeichnis eingebunden wird,
 // muss der Pfad relativ korrigiert werden.
 include $root.'func.php';
 include $root.'class.php';

 // timestamp to evaluate performance
 $millitimestamp0 = millitimestamp();
 
 // Optionen, die in Cookies gespeichert werden sollen
 $cookieopt[] = 'msg_status';
 $cookieopt[] = 'msg_time';
 $cookieopt[] = 'msg_number';
 $cookieopt[] = 'kalender_woche';
 $cookieopt[] = 'person_abc';
 $cookieopt[] = 'gruppe_abc';

 // object representing the connection to the database (db-layer)
 $db = new db();

 // manages login and session
 $user = new user();

 // Navigationslinks im HTML-HEAD
 $html_nav['start'] = array($root,'Startseite');
 $html_nav['help'] = array($root.'dokumentation/','Hilfe');
 $html_nav['up'] = array('../','Hinauf');
 $html_nav['author'] = array($root.'about.php','Impressum');
 // Arrays fr alle Titel und �erschriften
 $html_titel[0] = 'Infoschool';
 $html_headline[0] = 'Infoschool';
 // das Navigationsmen
 $html_menu['Startseite'] = './';
 $html_menu['Kalender'] = 'calendar/';
 $html_menu['Vertretungsplan'] = 'supply/';
 $html_menu['Forum'] = 'forum/';
 $html_menu['Dateiaustausch'] = 'dateien/';
 $html_menu['Messages'] = 'messages/';
// $html_menu['Zensuren'] = 'zensuren/';
 $html_menu['News'] = 'news/';
// $html_menu['Benutzer'] = 'benutzer/';
 $html_menu['Benutzer'] = 'users/';
 if(isset($_SESSION['admin']) && $_SESSION['admin']) $html_menu['Statistik'] = 'statistics.php';
 $html_menu['Hilfe'] = 'dokumentation/';
 $html_menu['FAQ'] = 'dokumentation/faq.php';
 $html_menu['Impressum'] = 'about.php';
 if(isset($_SESSION['admin']) && $_SESSION['admin']) $html_menu['Setup'] = 'setup/';
// $html_menu['Chat'] = 'chat/';
 // Mailheader
 $mailheader = 'From: "Infoschool" <noreply>'."\n";
 $mailheader.= 'Content-Type: text/plain; charset=UTF-8'."\n";
 // die deutsche Woche
 $woche[] = 'Sonntag';
 $woche[] = 'Montag';
 $woche[] = 'Dienstag';
 $woche[] = 'Mittwoch';
 $woche[] = 'Donnerstag';
 $woche[] = 'Freitag';
 $woche[] = 'Samstag';

// Variabeln fuer Benutzer Avatare
 $img_person_dir['intern'] = $root."img/person/";
 $img_person_dir['extern'] = "/img/person/";
 $max_avatar_groesse = 100000;

 // Code, der in HTML umgewandelt werden soll
 $code_html = array(
  'b' => '<b>$1</b>',
  'i' =>'<i>$1</i>',
  'u' => '<u>$1</u>',
  's' => '<s>$1</s>',
  'strong' => '<strong>$1'.'</strong>',
  'center' => '<div align='.'\"'.'center'.'\"'.'>$1</div>',
  'quote' => '<blockquote>$1</blockquote>',
  'code' => '<code>$1</code>',
  'pre' => '<pre>$1</pre>',
  'url' => '<a href='.'\"'.'%1%'.'\"'.' target='.'\"'.'_blank'.'\"'.'>$1</a>',
  'img' => '<img src='.'\"'.'%1%'.'\"'.' border=0 />',
 );
 $code_func = array(
  'url' => 'code_url',
  'img' => 'code_url',
 );

/////////////////////// Anfang OOP ///////////////////////

 // object containing every information needed for output
 // $output->out() formates everything and send it to the user
 $output = new output();


 // default language
 $output->lang = 'de';
 // navigation links
 $output->nav['start'] = $root;
 $output->nav['help'] = $root.'dokumentation/';
 $output->nav['up'] = '../';
 $output->nav['author'] = $root.'about.php';
 // title and headline
 $output->title[0] = 'infoschool';
 $output->headline[0] = 'infoschool';
 // navigation menu
 $output->menu['./'] = 'start';
 $output->menu['calendar/'] = 'calendar';
 $output->menu['supply/'] = 'supply schedule';
 $output->menu['forum/'] = 'Forum';
 $output->menu['dateien/'] = 'files';
 $output->menu['messages/'] = 'mail';
// $output->menu['zensuren/'] = 'grades';
 $output->menu['news/'] = 'news';
// $output->menu['benutzer/'] = 'users';
 $output->menu['users/'] = 'users';
 if(isset($_SESSION['admin']) && $_SESSION['admin']) $output->menu['statistics.php'] = 'statistics';
 $output->menu['dokumentation/'] = 'help';
 $output->menu['dokumentation/faq.php'] = 'faq';
 $output->menu['about.php'] = 'about';
 if(isset($_SESSION['admin']) && $_SESSION['admin']) $output->menu['setup/'] = 'setup';

 // Zeiteinteilung des Tages
 $timetable = new timetable();
 $timetable->load();
 $zeitplan = $timetable->data;
 unset($timetable);

 // subdir of the software
 $webdir = path_clean(path_rm_last($_SERVER['SCRIPT_NAME']).$root);
 // system-root for the user: http://server.domain.tld/foo/
 $http_root = 'http://'.$_SERVER['SERVER_NAME'].$webdir;

 // starts outstanding jobs
 $cron = new cron();

 // $output->out_of_service = true;
 if (isset($_GET['oos'])) $output->out_of_service = $_GET['oos'];
?>
