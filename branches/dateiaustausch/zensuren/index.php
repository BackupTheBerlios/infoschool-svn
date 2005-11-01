<?php
 include 'var.php';
 $html_headline[] = 'Pers&ouml;nliche Zensuren';

 function htmlformat_menu_jahr($z){
  global $jahr;
  if(sizeof($z)>0){
   $menu = '<p align="center">';
   foreach($z as $j => $zensur){
    $menu.= '| <a href="./?jahr='.$j.'">'.$j.'</a> |';
   }
   $menu.= '</p>';
  }
  else $j = '7';
  if(!$jahr) $jahr = $j;
  if(sizeof($jahr)>1) return $menu;
 }

 function get_zensuren($pid){
  get_mc();
  $zensuren = mysql_query('select id,jahr,gid,hj,m_s,punkte from zensuren where pid="'.$pid.'" order by jahr,gid,hj,m_s,id');
  mysql_close();
  while($z = mysql_fetch_array($zensuren)){
   $zensur[$z['jahr']][$z['gid']][$z['hj']][$z['m_s']][$z['id']] = $z['punkte'];
  }
  return $zensur;
 }

 function htmlformat_jahr($zensuren_jahr,$jahr){
  if(sizeof($zensuren_jahr)>0){
   $stellen = 2;
   $m_s = array('m','s');
   $input = '<input type="hidden" name="jahr" value="'.$jahr.'">';
   $summe_jahr = 0;
   foreach($zensuren_jahr as $gid => $halbjahr){
    $summe_gruppe = 0;
    $schnitt_gruppe = '';
    $gruppe = get_gruppe($gid);
    $v = array();
    $v['%name%'] = $gruppe[name];
    for($hj=0;$hj<3;$hj++){
     $summe_hj = 0;
     $schnitt_hj = '';
     for($i=0;$i<sizeof($m_s);$i++){
      $zensur = $halbjahr[$hj][$m_s[$i]];
      $z = '';
      $summe_ms = 0;
      $schnitt_ms = '';
      if(sizeof($zensur)>0){
       foreach($zensur as $id => $punkte){
        $z.= '<input type="text" size="2" maxlength="2" name="zensur['.$id.']" value="'.$punkte.'">&#160;';
        $summe_ms+= $punkte;
       }
       $schnitt_ms = round($summe_ms/sizeof($zensur),$stellen);
      }
      $z.= '<input type="text" size="2" maxlength="2" name="zensur_neu['.$gid.']['.$hj.']['.$m_s[$i].']">';
      $z.= '&#160;'.$schnitt_ms;
      $v['%zensuren'.$hj.$m_s[$i].'%'] = $z;
      $summe_hj+= $schnitt_ms;
     }
     $schnitt[$hj] = round($summe_hj/2,$stellen);
     $v['%schnitt'.$hj.'%'] = $schnitt[$hj];
    }
    $schnitt_gruppe = round(($schnitt[1]+$schnitt[2])/2,$stellen);
    $v['%schnitt12%'] = $schnitt_gruppe;
    $summe_jahr+= $schnitt_gruppe;
    $input.= get_face('gruppe.html',$v);
   }
   $input.= '<p><b>Jahresdurchschnitt: '.round($summe_jahr/sizeof($zensuren_jahr),$stellen).'</b></p>';
   $text = get_form($input,'&Auml;ndern','mod.php');
  }
  else{
   $text = '<p>F&uuml;r dieses Jahr wurden noch keine Zensuren eingetragen.</p>';
  }
  return $text;
 }

 if(session_is_registered('userid')){
  if($jahr = $_GET['jahr']) $_SESSION['zensuren_jahr'] = $jahr;
  else $jahr = $_SESSION['zensuren_jahr'];
  $zensuren = get_zensuren($_SESSION['userid']);
  $v['%menu_jahr%'] = htmlformat_menu_jahr($zensuren);
  $v['%zensuren%'] = htmlformat_jahr($zensuren[$jahr],$jahr);
  $v['%jahrgang%'] = get_select_int('jahr',7,14,$jahr);
  $v['%punktzahl%'] = get_select_int('punkte',0,16,0);
  $inhalt.= get_face('index.html',$v);
  $html_titel[] = $jahr;
  $html_headline[] = 'Jahrgang: '.$jahr;
 }else $inhalt = get_anmeldung_link();

 echo face($inhalt);
?>