<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';
 
 $output->secure();
 
 if (!isset($_GET['id'])) redirect('./');
 $id = $_GET['id'];
 if (!$id) redirect('./');

 $entry = new entry();
 $entry->load($id,$max_depth,'read');
 
 $v['entry'] = $entry->format('entry.html');
 $v['history'] = $entry->fdata['history'];
 $v['answers'] = format_answers($entry);
 
 $content = new tmpl('entry_answers.html',$v);

 $output->out($content);
?>
