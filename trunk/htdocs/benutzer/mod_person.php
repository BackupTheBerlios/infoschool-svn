<?php
 include 'var.php';
 function calc_scr_delay($delay)
 {
 $vorgabe = array(1000,800,600,400,200,100,75,50,25,1,0);
 return $vorgabe[$delay];
 }

 // ändert Personeninformationen
 function mod_person($person){
  $vorname = text2html($person['vorname']);
  $nachname = text2html($person['nachname']);
  if(checkdate($person['gebmon'],$person['gebtag'],$person['gebjahr'])){
   $gebdat = $person['gebjahr'].'-'.$person['gebmon'].'-'.$person['gebtag'];
  }

  $opt = '';
  for($i=0;$i<8;$i++){
   if(isset($person['opt'][$i])) $opt.='1';
   else $opt.='0';
  }
  $scr_wait = $person['scr_wait']*60000;
  $scr_delay = calc_scr_delay($person['scr_delay']);

  if (!stristr($person['homepage'], "http://"))$person['homepage'] =
"http://".$person['homepage'];
  
  
  $q = 'update person set
first_name="'.$vorname.'",last_name="'.$nachname.'",nid=';
  if($person['nk']) $q.= '"'.$person['nk'].'"';
  else $q.= 'null';
  $q.=
',birthday="'.$gebdat.'",mail="'.$person['mail'].'",icq="'.$person['icq'].'",opt="'.bindec($opt).'",scr_wait="'.$scr_wait.'",scr_delay="'.$scr_delay.'"
,description="'.$person['beschreibung'].'"
,homepage="'.$person['homepage'].'" ,class="'.$person['klasse'].'"  WHERE
id="'.$person['id'].'"';
  
  $mc = get_mc();

  mysql_query($q);
  if(mysql_affected_rows()) $_SESSION['meldungen'][] = 'Daten wurden
ge&auml;ndert.';
  else $_SESSION['error'][] = 'Daten wurden <u>nicht</u> ge&auml;ndert.';
  mysql_close($mc);
  redirect('view_person.php');
 }

 if(session_is_registered('userid')){
  if(is_array($person=$_POST['person'])){
   if($_SESSION['userid']==$person['id']){
    $_SESSION['first_name'] = $person['vorname'];
    $_SESSION['last_name'] = $person['nachname'];
    $_SESSION['name'] = $person['vorname'].' '.$person['nachname'];
    mod_person($person);
   }
   else{
    redirect('view_person.php?pid='.$_SESSION['userid']);
   }
  }
  else{
   redirect('view_person.php');
  }
 }
 else $inhalt = get_anmeldung_link();

 echo face($inhalt);
?>