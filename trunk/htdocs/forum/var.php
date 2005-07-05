<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
/* Forum
  Welcome to this forum!
  Most fora in the net have a flat structure:
   forum -> thread -> post
  This structure works well, but how look real discussions?
  In your mind is a statement. And there can be a number of answers to this statement. Ok?
  Every answer is a statement itself and now we are at the beginning. That's a very short loop, huh?
  As short this loop is, as simple this forum is. The statements are named entries. The whole forum consists of entries and entries relating to other entries, nothing more.
  The one and only complicate thing is, that this forum has a tree structure while the tables in the database has only two dimensions.
  Ok, there is another complicate thing, rights. But rights are complicate in every case. That's the reason because politics never work right. :)
  
  The table 'forum' has all information this forum needs. But to read this information and to construct the forum tree take a very long time.
  | id | rel_to | misc...
  | 1  | 0      | That is the first entry in the forum.
  | 2  | 1      | That is the second entry and an answer of the first.
  | 3  | 2      | That is the answer of the second entry.
  | 4  | 2      | That is the second answer of the second entry.
  | 5  | 3      | That is an answer of the entry with the id 3.
  Imagine you want to display entry 2. You need the entries before (1) and its answers (3, 4, 5).
  You read all entries and struct them in a tree. But there can be thousands of other entries you do not need. Thousands of entries make your Skrip very slow.
  So, how can we fetch exact the entries we neeed?
  We need an additional table called 'forum_relation'. This table knows all answers to one entry, even non direct answers.
  | entry | answer | level |
  | 2     | 3      | 1     |
  | 2     | 4      | 1     |
  | 2     | 5      | 2     |
  Now we can select all entries before the actual entry ('... where answer=actual_id').
  And we can select all answers to this entry ('... where entry=actual_id'). This can also be restricted through the level.
  
*/
/* The forum uses right tables with the following bit options.
 1      1       read
 2      2       answer
 3      4       edit own
 4      8       delete own
 5      16     change rights of own
 6      32     edit
 7      64     delete
 8      128   change rights
 Every entry can have own rights. Every entry without own rights inherits them from the superior entry.
 To prevent loosing an entry by giving wrong rights every entry has an admin. An admin can read the entry and change its rights.
 The admin of a root entry (relating to 0) is the author of this entry and can't loose his rights.
 All answers to this root entry have the same admin.
*/
 include 'func.php';
 include 'class.php';
 if (!isset($root)) $root = '';
 $root.= '../';
 include $root.'var.php';

 $output->headline[] = 'Forum';

?>
