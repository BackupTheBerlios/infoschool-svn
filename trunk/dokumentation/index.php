<?php
 include 'var.php';

 $output->headline[] = 'documentation';

 // -- obsolete
 // formatiert einen Menpunkt
 function htmlformat_menuitem($root,$s,$u,$c){
  $v['%sign%'] = $s;
  $v['%url%'] = $u;
  $v['%caption%'] = $c;
  $a = Path::absolute($u);
  $b = Path::absolute($root.substr($_SERVER['REQUEST_URI'],1));
  if($a==$b) $item = FileReader::readFile($root.'menulinkv.html',$v);
  else $item = FileReader::readFile($root.'menulink.html',$v);
  return $item;
 }

 // -- obsolete
 // rekursive Funktion, gibt zusammengeh�ige Menpunkte zurck
 // und ruft sich fr Untermens wieder auf
 function get_menuitem($root,$url,$caption='',$s=''){
  $item = '';
  if(is_array($url) && sizeof($url)>0){
   if($caption){
    $item = htmlformat_menuitem($root,$s.'++',$url['0'],$caption);
    unset($url['0']);
   }
   foreach($url as $c => $u){
    $item.= get_menuitem($root,$u,$c,'&#160;'.$s);
   }
  }
  else{
   $item = htmlformat_menuitem($root,$s.'+-',$url,$caption);
  }
  return $item;
 }

 $v['menu'] = get_menuitem($root,convert_menu($dok_menu));

 $content = new tmpl('index.html',$v);

 $output->out($content);
?>