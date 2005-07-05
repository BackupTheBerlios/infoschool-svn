<?php
 include 'var.php';

 // gibt Anträge auf neue Accounts zum Bearbeiten in einer Tabelle aus
 function get_neu_account_form($account){
  $input = '<table border class="cells_w">'."\n";
  $input.= '<tr><th>Nachname</th><th>Vorname</th><th>annehmen</th><th>ablehnen</th><th>unentschieden</th></tr>'."\n";
  for($i=0;$i<sizeof($account);$i++){
   $input.= '<tr><td>'.$account[$i][3].'</td><td>'.$account[$i][2].'</td><td><input type="radio" name="account['.$account[$i][0].'][accept]" value="1"></input></td><td><input type="radio" name="account['.$account[$i][0].'][accept]" value="-1"></input></td><td><input type="radio" name="account['.$account[$i][0].'][accept]" value="0" checked></input></td></tr>'."\n";
  }
  $input.= '</table>'."\n";
  return get_form($input,'Bearbeiten');
 }

 // trägt neue Accounts bei Bedarf in person ein und löscht die Anträge
 function rm_neu_account($account){
  if(sizeof($account)>0){
   $mc = get_mc();
   foreach($account as $id => $a){
    if($a[accept]){
     $arow = mysql_query('select passwd,vorname,nachname,gebdat,mail from neu_account where id="'.$id.'"');
     list($passwd,$vorname,$nachname,$gebdat,$mail) = mysql_fetch_row($arow);
     if($a[accept]=='1'){
     $query = 'insert into person (passwd, first_name, last_name, birthday,mail)values("'.$passwd.'","'.$vorname.'","'.$nachname.'","'.$gebdat.'","'.$mail.'")';
      mysql_query($query);
      $pid = mysql_insert_id($mc);
      mysql_query('insert into pg values("'.$pid.'","1")');
      $text = 'Ihr Account wurde freigeschaltet. Bisherige Benutzerdaten:'."\n";
      $text.= 'ID: '.$pid."\n";
      $text.= 'Vorname: '.html2text($vorname)."\n";
      $text.= 'Nachname: '.html2text($nachname)."\n";
      $text.= 'Geburtsdatum: '.dt2datum($gebdat,4)."\n";
      $text.= 'E-Mailadresse: '.$mail;
     }
     else{
      $text = 'Ihr Antrag auf einen Account wurde abgelehnt.';
     }
     $mailbody = get_mailbody(html2text($vorname.' '.$nachname),$text);
     mail($mail,'[Infoschool] Neuer Account',$mailbody,$GLOBALS['mailheader']);
     mysql_query('delete from neu_account where id="'.$id.'"');
    }
   }
   mysql_close($mc);
   redirect('neu_person.php');
  }
 }

 if(session_is_registered('userid')){
  if($_SESSION['admin']){
   if(is_array($account=$_POST['account'])){
    rm_neu_account($account);
    unset($account);
   }
   $html_headline[] = 'Antr&auml;ge auf neue Accounts';
   $inhalt = get_neu_account_form(get_neu_account());
  }
  else{
   $inhalt = '<p>Nur Administratoren d&uuml;rfen &uuml;ber Accountantr&auml;ge entscheiden</p>';
  }
 }
 else{
  $inhalt = get_anmeldung_link();
 }

 echo face($inhalt);
?>