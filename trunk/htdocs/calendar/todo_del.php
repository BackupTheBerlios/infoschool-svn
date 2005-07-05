<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */
 include 'var.php';

 // delete a todo entry
 function del_todo($tid){
  $todo = new todo();
  $todo->delete($tid);
 }

 $output->secure();

 if(isset($_GET['tid'])) del_todo($_GET['tid']);

 repulse();
?>