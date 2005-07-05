<?php
 include 'var.php';

 $html_menu = add_subdir($html_menu);
 $html_titel[] = 'FAQ';
 $html_headline[] = '<a title="Frequently Asked Questions (H&auml;ufig gestellte Fragen)">FAQ</a>';
 $inhalt = get_face('faq.html');

 echo face($inhalt);
?>