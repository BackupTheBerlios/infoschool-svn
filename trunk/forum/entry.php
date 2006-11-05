<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 include 'var.php';
 
 $output->secure();
 
 $entry = new entry();
 $entry->get();
 $entry->load();
 $entry->check_right('read');
 $entry->load_answers($max_depth,'read');
 
 $v['entry'] = $entry->format('entry.html');
 $v['history'] = $entry->fdata['history'];
 $v['answers'] = format_answers($entry);
 
 $content = new tmpl('entry_answers.html',$v);

 $output->out($content);
?>
