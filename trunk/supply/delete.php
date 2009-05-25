<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2009 Maikel Linke, Christian Zedler
 */
include 'var.php';

$output->secure('admin');
$supply->load_list();

if (count($supply->list) > 0) {
    $supply->delete($_GET['day']);
}

redirect('./');

?>