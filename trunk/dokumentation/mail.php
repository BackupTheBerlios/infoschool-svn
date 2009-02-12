<?php
include 'var.php';

if($_POST['text']){
	/*
	 * Attention!
	 * The fields mail and name are interchanged. Actually this keeps a lot of spam away.
	 */
	if (!strstr($_POST['mail'],'@')) {
		$header = 'From: ';
		if($_POST['mail']) $header.= $_POST['mail'];
		if($_POST['name']) $header.= ' <'.$_POST['name'].'>';
		mail('infoschool','[Infoschool] Nachricht an die Entwickler',stripslashes($_POST['text']),$header);
	}
}

redirect('./');
?>