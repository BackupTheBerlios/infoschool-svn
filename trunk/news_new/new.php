<?php/* * This file is part of Infoschool - a web based school intranet. * Copyright (C) 2005 Maikel Linke */ include 'var.php';  $output->secure();  $news = new news();  if (isset($_POST['news_data'])) {  $data = $_POST['news_data'];  $news->insert($data);  redirect('own.php'); }  $news->create(); $news->format(); $content = $news->tmpl('form_new.html');   $output->out($content); ?>