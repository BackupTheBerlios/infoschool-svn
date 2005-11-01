<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->secure('user',true);

 $font_size = 13;
 $show_teachers = false;

 if (isset($_GET['font_size'])) $font_size = (int) $_GET['font_size'];
 if (isset($_GET['fields'])) $field_num = (int) $_GET['fields'];
 if (isset($_GET['skip_teacher'])) $show_teachers = !((boolean) $_GET['skip_teacher']);
 if (isset($_GET['show_teachers'])) $show_teachers = (boolean) $_GET['show_teachers'];

 $supply->format_fields($font_size,$show_teachers);

 $tmpl = new tmpl('show.html',$supply->fdata);
 $output->output = $tmpl->fdata;
 $output->send();
?>