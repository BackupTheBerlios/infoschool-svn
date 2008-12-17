<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2008 Maikel Linke
 */

/**
 * Description of TutorialLoader
 *
 * @author maikel
 */
class TutorialLoader {

    public static function load($fileName) {
        $filePath = 'html/'.$fileName;
        if (!is_readable($filePath)) return;
        $fileSize = filesize($filePath);
        if ($fileSize < 1) return '';
        $filePointer = fopen($filePath, 'r');
        $fileData = fread($filePointer, $fileSize);
        fclose($filePointer);
        return $fileData;
    }
}
?>
