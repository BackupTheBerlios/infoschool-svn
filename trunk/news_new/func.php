<?php/* * This file is part of Infoschool - a web based school intranet. * Copyright (C) 2005 Maikel Linke */  function select_news($mode) {  global $db;  $where = '';  if ($mode == 'now') {   $where = 'news.active=1 and             news.start<=now() and              news.end>now()';  }  if ($mode == 'past') {   $where = 'news.active=1 and             news.end<now()';  }  if ($mode == 'future') {   $where = 'news.active=1 and             news.start>now()';  }  if ($mode == 'inactive') {   $where = 'news.active=0';  }  if ($mode == 'own') {   $where = 'news.author='.$_SESSION['userid'];  }  $query = 'news.id,             news.active,             news.start,            news.end,             news.author,            person.first_name as author_first_name,            person.last_name as author_last_name,             news.topic,             news.text            from news            join person on            news.author=person.id           where            '.$where.'           order by news.start desc';   $db->select($query);  return $db->data; }  function select_format_news($mode) {  $news_data = select_news($mode);  $content = '';  foreach ($news_data as $i => $data) {   $news = new news($data);   $news->format(true);   $content.= $news->tmpl();  }  if ($content == '') {    $content = '{no news}';  }  return $content; } ?>