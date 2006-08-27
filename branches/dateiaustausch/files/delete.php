<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 include 'var.php';
 
 $output->secure();
 
 if (!isset($_GET['item'])) redirect('./');
 
 $item_id = (int) $_GET['item'];
 $item = new fs_item($item_id);
 if (!$item->right_delete()) redirect('./');
 $item->delete();
 redirect('./?dir='.$item->data['rel_to']);
?>
