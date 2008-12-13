<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2008 Maikel Linke
 */
include 'var.php';
require_once $root.'class_user.php';

if(session_is_registered('userid')){
	user::loadOptionsFromCookies();
}
redirect($_GET['origin']);
?>