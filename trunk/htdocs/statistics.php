<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */
 include 'var.php';

 $output->secure();

 $output->title[] = 'statistics';
 $output->headline[] = 'statistics';

 get_mc();
 $prow_zahl = mysql_query('select count(id) from person');
 $pletzter = mysql_query('select last_login from person order by last_login desc limit 1');
 $prow_wartend = mysql_query('select count(id) from neu_account');
 $prow_nie = mysql_query('select count(id) from person where last_login="0000-00-00 00:00:00"');
 $prow_heute = mysql_query('select count(id) from person where last_login>"'.date('Y-m-d').' 00:00:00"');
 $prow_24h = mysql_query('select count(id) from person where last_login>"'.date('Y-m-d H:i:s',strtotime('-24 hours')).'"');
 $prow_woche = mysql_query('select count(id) from person where last_login>"'.date('Y-m-d H:i:s',strtotime('-1 weeks')).'"');
 $prow_monat = mysql_query('select count(id) from person where last_login>"'.date('Y-m-d H:i:s',strtotime('-1 months')).'"');
 $prow_irgendwann = mysql_query('select count(id) from person where last_login>"0000-00-00 00:00:00"');
 $gzahl = mysql_query('select count(id) from gruppe');
 $grow_nalle = mysql_query('select count(person.id) from person left join pg on person.id=pg.pid and pg.gid=1 where pg.pid is null');
 $g_broken = mysql_query('select count(pg.pid) from pg left join person on pg.pid=person.id where person.id is null');
// $g_broken_ = mysql_query('select pg.pid from pg left join person on pg.pid=person.id where person.id is null');
// while ($row = mysql_fetch_row($g_broken_)) {
//  mysql_query('delete from pg where pid='.$row[0]);
// }
 $mzahl = mysql_query('select count(id) from msg');
 $m_ur = mysql_query('select count(id) from msg where status&6=6');
 $m_del = mysql_query('select count(id) from msg where status&3=0');
 $m_del_emp = mysql_query('select count(id) from msg where status&2=0');
 $m_del_ges = mysql_query('select count(id) from msg where status&1=0');
 list($v['pzahl']) = mysql_fetch_row($prow_zahl);
 list($v['p_letzter']) = mysql_fetch_row($pletzter);
 list($v['p_nie']) = mysql_fetch_row($prow_nie);
 list($v['p_wartend']) = mysql_fetch_row($prow_wartend);
 list($v['p_heute']) = mysql_fetch_row($prow_heute);
 list($v['p_24h']) = mysql_fetch_row($prow_24h);
 list($v['p_woche']) = mysql_fetch_row($prow_woche);
 list($v['p_monat']) = mysql_fetch_row($prow_monat);
 list($v['p_irgendwann']) = mysql_fetch_row($prow_irgendwann);
 list($v['gzahl']) = mysql_fetch_row($gzahl);
 list($v['g_nalle']) = mysql_fetch_row($grow_nalle);
 list($v['g_broken']) = mysql_fetch_row($g_broken);
 list($v['mzahl']) = mysql_fetch_row($mzahl);
 list($v['m_ur']) = mysql_fetch_row($m_ur);
 list($v['m_del']) = mysql_fetch_row($m_del);
 list($v['m_del_emp']) = mysql_fetch_row($m_del_emp);
 list($v['m_del_ges']) = mysql_fetch_row($m_del_ges);

 $tables_result = mysql_query('show tables');
 $v['tables'] = mysql_num_rows($tables_result);
 while ($row = mysql_fetch_row($tables_result)) {
  $tbl_name = $row[0];
  $table_count_result = mysql_query('select count(*) from '.$tbl_name);
  $table_count = mysql_fetch_row($table_count_result);
  $v['tbl'][] = array(
    'table_name' => $tbl_name,
    'table_entries' => $table_count[0],
  );
 }

 mysql_close();

 $content = new tmpl('statistics.html',$v);
 $output->out($content);
?>