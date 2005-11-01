<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */
 include 'var.php';

 $output->secure();
 $output->title[] = 'online users';
 $output->headline[] = 'online users';

 $online_users = active_sessions();
 $vars['user'] = $online_users;
 $content = new tmpl('online_users.html',$vars);

 $output->out($content);
?>