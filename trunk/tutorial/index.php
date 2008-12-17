<?php echo '<?xml version="1.0" ?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Content-Style-Type" content="text/css" />
  <meta http-equiv="expires" content="0" />
  <meta http-equiv="pragma" content="no-cache" />
  <meta http-equiv="content-language" content="de" />
  <meta name="description" content="PHP-Tutorial mit dem Ziel an Infoschool zu lernen." />
  <meta name="keywords" content="PHP, tutorial, howto, infoschool, lernen" />
  <meta name="robots" content="index,follow" />
  <link rel="shortcut icon" href="../img/IS_16.png" />
  <link rel="stylesheet" type="text/css" href="style.css" />
  <title>Infoschool-Tutorial - Lerne PHP anhand von Infoschool</title>
 </head>
 <body>
  <div class="text">
<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2008 Maikel Linke
 */

require_once 'TutorialLoader.php';

function getPage() {
    $standardPage = 'start.html';
    $page = &$_GET['page'];
    if (isset($page)) { 
        if (ereg('^([a-zA-Z0-9_\-]+)$', $page)) {
            return $page.'.html';
        }
    }
    return $standardPage;
}

echo TutorialLoader::load(getPage());

?>
  </div>
 </body>
</html>
