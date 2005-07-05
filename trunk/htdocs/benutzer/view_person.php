<?php
 include 'var.php';

 // gibt Optionenvariablen für das Formular zurück
 function get_opt_selected($opt){
  for($i=0;$i<8;$i++){
   if(substr($opt,$i,1)) $o['%opt'.$i.'%'] = ' checked';
   else $o['%opt'.$i.'%'] = '';
  }
  return $o;
 }

 function calc_scr_delay($delay)
 {
 $vorgabe =
array(1000=>0,800=>1,600=>2,400=>3,200=>4,100=>5,75=>6,50=>7,25=>8,1=>9,0=>10);
 return $vorgabe[$delay];
 }

 // liefert ein Formular um Personendaten zu ändern
 function get_person_form($person){
  $v = get_opt_selected($person['opt']);
  $v['%id%'] = $person['id'];
  $v['%vorname%'] = $person['vorname'];
  $v['%nachname%'] = $person['nachname'];
  $v['%nk%'] = $person['nk'];
  $v['%gebdat%'] =
get_select_int('person[gebtag]',1,32,substr($person['gebdat'],8,2));
  $v['%gebdat%'].=
get_select_int('person[gebmon]',1,13,substr($person['gebdat'],5,2));
  $v['%gebdat%'].=
get_select_int('person[gebjahr]',date(Y)-65,date(Y)-5,substr($person['gebdat'],0,4));
  $v['%mail%'] = $person['mail'];
  $v['%icq%'] = $person['icq'];
  $v['%scr_wait%'] =
get_select_int('person[scr_wait]',0,31,round($person['scr_wait']/60000));
  $v['%scr_delay%'] =
get_select_int('person[scr_delay]',0,11,calc_scr_delay($person['scr_delay']));
  $v['%beschreibung%'] = $person['beschreibung'];
  $v['%homepage%'] = $person['homepage'];
  $v['%klasse%'] = $person['klasse'];
  
  $v['%bild%']= ''.get_avatar_link($person['id']);
  
  $input = get_face('view_person_form.html',$v);
  $return = get_form($input,'&Auml;ndern','mod_person.php');
  
  $return .= get_face('view_person_form_avatar.html', $v);

  $input = get_face('view_person_form_passwd.html', $v);
  $return .= get_form($input,'&Auml;ndern','mod_person_passwd.php');

return $return;
 }

 // gibt Personeninformationen formatiert aus
 function get_person_info($person){
  
  $v['%bild%']= ''.get_avatar_link($person['id']);
  
  if(strlen($person['gebdat'])>0) $v['%gebdat%'] = '<b>Geburtsdatum:</b>
'.htmlformat_datum($person['gebdat'],4).'<br>';
  	else $v['%gebdat%'] = '';
  if(strlen($person['mail'])>3) $v['%mail%'] = '<b>E-Mailadresse:</b> <a
href="mailto:'.$person['mail'].'">'.$person['mail'].'</a><br>';
  	else $v['%mail%'] = '';
  if(strlen($person['icq'])>1) $v['%icq%'] = '<b>ICQ-Nummer:</b>
'.$person['icq'].'<br>';
  	else $v['%icq%'] = '';
  if(strlen($person['homepage'])>7) $v['%homepage%'] = '<b>Homepage:</b> <a
href="'.$person['homepage'].'">'.$person['homepage'].'</a><br>';
  	else $v['%homepage%'] = '';
  if($person['klasse'] > 0) $v['%klasse%'] = '<b>Klasse/ Jahrgang:</b>
'.$person['klasse'].'<br>';
	else $v['%klasse%'] = '';
  if(strlen($person['beschreibung'])>0)	$v['%beschreibung%'] =
'<p>'.$person['beschreibung'].'</p>';
  	else $v['%beschreibung%'] = '';
  
  $return = get_face('view_person_info.html', $v);
 
  return $return;
 }

 if(session_is_registered('userid')){
  if($_GET['pid'])$pid=$_GET['pid'];
  else$pid = $_SESSION['userid'];
  if(is_array($person=get_person($pid,0,0,$_GET['pid']))){
   $html_titel[] = 'Person: '.$person['vorname'].' '.$person['nachname'];
   $html_headline[] = $person['name'].($person['nk']?'
('.$person['nk'].')':'').' <a
href="../messages/neu.php?typ=person&id='.$person['id'].'">'.htmlformat_symbol('mail','Message
Schicken').'</a>&#160;';
   


   if($person['id']==$_SESSION['userid'] && !isset($_GET['pid'])){
    $vars['%info%'] = get_person_form($person);
    $vars['%remove%'] = get_form('','Meinen Account
L&ouml;schen','rm_person.php');
   }
   
   else{
    $vars['%info%'] = get_person_info($person);
    $vars['%remove%'] = '';
	
    if($_SESSION['admin'])
    	$vars['%remove%'] = get_form('<input type="hidden" name="pid"
value="'.$person[id].'"></input>','Diesen Account
L&ouml;schen','rm_person.php');
   }


   $vars['%gruppen%'] = '<h2>Gruppen</h2>';

   $gruppen = get_gruppen($person['id']);
   if(sizeof($gruppen)>0){
    $vars['%gruppen%'] .= '<p><b>Mitglied in folgenden Gruppen:</b></p>';
    $vars['%gruppen%'].= get_gruppen_link($gruppen);
   }
   else $vars['%gruppen%'] .= '<p>In noch keiner Gruppe Mitglied.</p>';
   
   $inhalt = get_face('view_person.html',$vars);

  }
 }
 else{
  $inhalt = get_anmeldung_link();
 }

 echo face($inhalt);
?>