<?php
include 'var.php';
require_once $root.'class_file.php';

$html_titel[] = 'FAQ';
$html_headline[] = '<a title="Frequently Asked Questions (H&auml;ufig gestellte Fragen)">FAQ</a>';
$inhalt = FileReader::readFile('faq.html');

$output->out('<h1><a title="Frequently Asked Questions (H&auml;ufig gestellte Fragen)">FAQ</a></h1>'.$inhalt);
?>