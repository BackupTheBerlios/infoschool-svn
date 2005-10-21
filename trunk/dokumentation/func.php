<?php

 // macht aus pfaden urls, mit denen die pfade an show.php übergeben werden
 function convert_menu($m){
  if(is_array($m)){
   if(sizeof($m)>0){
    foreach($m as $k => $v){
     $m[$k] = convert_menu($v);
    }
   }
  }
  else{
   $m = 'show.php?dok='.$m;
  }
  return $m;
 }

?>