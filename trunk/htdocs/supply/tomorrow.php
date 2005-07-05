<?php
/*
Infoschool - a web based school intranet
Copyright (C) 2004 Maikel Linke, Christian Zedler

This file is part of Infoschool.

Infoschool is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

Infoschool is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.
*/

 include 'var.php';

 if(session_is_registered('userid')){
  $vplan = get_vplan(1);
  $vplan = vplan2html_single($vplan['text']);
  $vars['%url%'] = sessionurl('tomorrow.php');
  $vars['%titel%'] = 'Vertretungsplan';
  $vars['%vplan%'] = $vplan;
  echo get_face('single.html',$vars);
  } else echo 'Diese Funkion ist nur nach dem Login verfuegbar.';
?>
