<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 include 'var.php';
 require_once 'new_files.php';
 
 $output->secure();
 
 $new_files = new new_files();
 $output->out($new_files->toTmpl());
 
?>