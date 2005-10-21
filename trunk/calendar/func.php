<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */

 // errechnet, in der wievielten Woche (von jetzt) ein Datum liegt
 // da der Unterschied zwischen Sommer- und Winterzeit eine Verschiebung verursachen kann,
 // wird die Uhrzeit des Datum ignoriert und auf 12 gesetzt
 function w_dif($date){
  list($day,$time) = explode(' ',$date);
  $w = floor((strtotime($day.' 12')-strtotime(date('Y-m-d')))/60/60/24/7);
  return ($w<0)?0:$w;
 }

?>