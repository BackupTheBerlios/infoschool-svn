<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2009 Maikel Linke
 */

class supply {
	var $table = 'supply_schedule_files';
	var $day;
	var $date;
	var $list = array();
	var $text;
	var $line = array();
	var $menu = array();
	var $linedata = array();
	var $teacher_offset = 0;
	var $lessonlines = array('classes'=>array(),'teachers'=>array());
	var $nameids = array();
	var $lessons = array();

	function daynum($date) {
		if (!$date) return 0;
		$date_today = date('Y-m-d');
		$ts = strtotime($date);
		$ts_today = strtotime($date_today);
		$ts_dif = $ts - $ts_today;
		$day_dif = $ts_dif / 60 / 60 / 24;
		$daynum = round($day_dif);
		return $daynum;
	}

	function load_list() {
		$query = 'date, text from '.$this->table.' where date>="'.date('Y-m-d').'" order by date';
		global $db;
		$db->select($query);
		$this->list = $db->data;
		$this->get_day();
	}

	function get_day($day=false) {
		if (isset($_GET['day'])) $_SESSION['calendar_day'] = $_GET['day'];
		if (isset($_SESSION['calendar_day'])) {
			$day = $_SESSION['calendar_day'];
		}
		else {
			if (!is_numeric($day)) {
				$day = 0;
				if (date('H') > 15 && isset($this->list[1])) $day = 1;
			}
		}
		$this->set_day($day);
	}

	function set_day($day=0) {
		$this->day = (int) $day;
		if (!isset($this->list[$day])) return;
		$schedule = $this->list[$day];
		$this->date = $schedule['date'];
		$this->text = $schedule['text'];
	}

	function delete($day) {
		if (!isset($this->list[$day])) return;
		$schedule = $this->list[$day];
		global $db;
		$query = 'delete from ' . $this->table
		. ' where date = "' . $schedule['date'] . '";';
		$db->query($query);
	}

	function format_menu() {
		$today = date('Y-m-d');
		$menu = array();
		foreach ($this->list as $day => $schedule) {
			$date = $schedule['date'];
			$entry[0] = array(
      'dow' => day_of_week($date),
      'date' => tmpl_date_title($date,'md'),
      'day' => $day
			);
			$link = $nolink = array();
			if ($date == $this->date) {
				$nolink = $entry;
			}
			else {
				$link = $entry;
			}
			$menu[] = array(
      'menu_nolink' => $nolink,
      'menu_link' => $link,
			);
		}
		$this->menu = $menu;
	}

	function text2line() {
		$this->teacher_offset = 0;
		$text = substr($this->text,1,-56);
		$html = mask_html($text);
		$all_lines = explode("\r\n",$html);
		$array = array();
		foreach ($all_lines as $i => $text) {
			$text = trim($text);
			$text = substr($text,1);
			$line = array();
			$line['text'] = $text;
			$line['class'] = '';
			$array[$i] = $line;
			if ($text == 'Betroffen:') $this->teacher_offset = $i;
		}
		$this->line = $array;
	}

	function analyse() {
		$this->analyse_important();
		if ($this->teacher_offset) {
			$this->analyse_lessonlines('classes',0,$this->teacher_offset);
			$this->analyse_lessonlines('teachers',$this->teacher_offset,count($this->line));
		}
		foreach ($this->line as $i => $line) {
			$this->line[$i]['text'] = str_replace(' ','&nbsp;',$line['text']);
		}
	}

	function analyse_important() {
		$mark = '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!';
		$important = false;
		foreach ($this->line as $i => $line) {
			$text = $line['text'];
			if ($text == $mark) $important = !$important;
			if ($text == $mark || $important) $this->line[$i]['class'] = 'important';
		}
	}

	function analyse_lessonlines($sort,$start,$end) {
		for ($i=$start;$i<$end;$i++) {
			$this->analyse_lessonline($i,$sort);
		}
	}

	function analyse_lessonline($i,$sort) {
		static $headline;
		$line = &$this->line[$i];
		$text = &$line['text'];
		$line['sort'] = $sort;
		if (!$text) $relates = '';
		if ($text == '         Studientag') {
			$line['class'] = 'lesson';
			$line['info'] = trim($text);
		}
		if (substr($text,0,2) == '  ' && substr($text,2,1) != ' ') {
			$line['class'] = 'lesson';
			$line['info'] = trim($text);
		}
		if (preg_match('/^ +([0-9]{1,2})\.Std[\.|\:] (\S+) (.+)$/',$text,$result)) {
			$line['class'] = 'lesson';
			$line['lesson'] = $result[1];
			$line['nameid'] = $result[2];
			$line['info'] = $result[2].' '.$result[3];
		}
		if ($line['class'] == 'lesson') {
			if (isset($this->line[$i-1])) {
				$prevline = &$this->line[$i-1];
				if ($prevline['text'] && $prevline['text'][0] != ' ') {
					$headline = $prevline['text'];
					$headline_class = $headline;
					if ($sort == 'classes') {
						if ((int) $headline) {
							$headline_class = (int) $headline;
						}
						if (substr($headline,0,9) == 'Jahrgang ') {
							$headline_class = (int) substr($headline,9);
						}
					}
					$prevline['class'] = 'cl'.$headline_class;
				}
				$line['headline'] = $headline;
			}
			if (isset($line['lesson'])) {
				if (isset($this->line[$i+1])) {
					$nextline = &$this->line[$i+1];
					if (substr($nextline['text'],0,7) == '       ') {
						$nextline['class'] = 'lesson';
						$line['info'].= ' '.trim($nextline['text']);
					}
				}
			}
		}
	}

	function format_fields($font_size=0,$show_teachers=false) {
		$this->load_list();
		$this->text2line();
		$this->analyse();
		$line = $this->line;
		if ($this->teacher_offset && !$show_teachers) {
			$line = array_slice($line,0,$this->teacher_offset);
		}
		else {
			array_splice($line,5,($this->teacher_offset-5));
		}
		$lines = count($line);
		while ($lines && $line[$lines-1] == '') {
			unset($line[$lines-1]);
			$lines = count($line);
		}
		$height = 1024 - 6;
		if (!$font_size) $font_size = 13;
		$line_height = $font_size + 1;
		$lines_per_col = floor($height / $line_height);
		$field = array();
		$field_i = 0;
		$begin = 0;
		while ($begin < $lines) {
			$field[$field_i]['line'] = array_slice($line,$begin,$lines_per_col);
			$field_i++;
			$begin = $lines_per_col * $field_i;
		}
		$fdata['font_size'] = $font_size;
		$fdata['line_height'] = $line_height;
		$fdata['field'] = $field;
		$fdata['table_class'] = '';
		$timestamp_yesterday = strtotime('-1 day');
		$date_yesterday = date('Y-m-d 18:00:00',$timestamp_yesterday);
		$timestamp_old = strtotime($date_yesterday);
		$printline = $line[2]['text'];
		$printdate_mark = 'Drucktermin:&nbsp;';
		$printdate_pos = strpos($printline,$printdate_mark) + strlen($printdate_mark);
		$printed_de = substr($printline,$printdate_pos,24);
		list($date_de,$time) = explode('&nbsp;',$printed_de);
		list($day,$month,$year) = explode('.',$date_de);
		$printed = $year.'-';
		$printed.= $month.'-';
		$printed.= $day;
		$printed.= ' '.$time;
		$timestamp = strtotime($printed);
		if ($timestamp > $timestamp_old) {
			$fdata['table_class'] = 'new';
		}
		$this->fdata = $fdata;
	}

	function import($fileid) {
		if (!isset($_FILES[$fileid]) || $_FILES[$fileid]['size'] == 0) return false;
		$this->text = file_data($_FILES[$fileid]['tmp_name']);
		$this->text = utf8_encode($this->text);
		$this->text2line();
		$this->extract_date();
		$this->insert();
		$this->load_nameids();
		$this->text2line();
		$this->analyse();
		$this->import_classes();
		$this->import_teachers();
		$_SESSION['notice'][] = 'supply schedule imported';
		return true;
	}

	function extract_date() {
		$dateline = $this->line[0]['text'];
		$parts = explode(' ',$dateline);
		$day_month = $parts[2];
		list($day,$month) = explode('.',$day_month);
		$timestamp = strtotime(date('Y').'-'.$month.'-'.$day);
		if ($timestamp < strtotime('-1 day')) {
			$timestamp = strtotime((date('Y')+1).'-'.$month.'-'.$day);
		}
		$date = date('Y-m-d',$timestamp);
		$this->date = $date;
	}

	function load_nameids() {
		$day = day_of_week($this->date);
		global $db;
		$select_old = "person.id as person_id, person.nk as nameid, timetable.name as lesson_name, stunde.id as lesson_id FROM
                   person, pg,gruppe, stunde,timetable WHERE
                    person.nk is not null AND
                    person.id=pg.pid AND
                    pg.gid=gruppe.id AND
                    gruppe.id=stunde.gid AND
                    stunde.zeit=timetable.time AND
                    stunde.tag='".$day."'";
		$select = "person.id as person_id, person.nid as nameid, timetable.name as lesson_name, lesson.id as lesson_id FROM
                   person, pg,gruppe, lesson,timetable WHERE
                    person.nid is not null AND
                    person.id=pg.pid AND
                    pg.gid=gruppe.id AND
                    gruppe.id=lesson.gid AND
                    lesson.time=timetable.time AND
                    lesson.day='".$day."'";
		$db->select($select);
		$nameids = $lessons = array();
		foreach ($db->data as $i => $entry) {
			$nid = $entry['nameid'];
			$lesson = $entry['lesson_name'];
			$nameids[$nid] = $entry['person_id'];
			$lessons[$nid][$lesson] = $entry;
		}
		$this->nameids = $nameids;
		$this->lessons = $lessons;
	}

	function insert() {
		global $db;
		$query = $this->table.' where date="'.$this->date.'"';
		$db->delete($query);
		$query = $this->table.' (date, text) values ("'.$this->date.'","'.$this->text.'")';
		$db->insert($query);
	}

	function import_classes() {
		$lessons = $this->lessons;
		global $db;
		$db->query('delete from vertretung where datum="'.$this->date.'"');
		foreach ($this->line as $i => $line) {
			if (!isset($line['sort'])) continue;
			if ($line['sort'] != 'classes') continue;
			if ($line['class'] != 'lesson') continue;
			if (isset($line['lesson']) && isset($line['nameid'])) {
				$nameid = $line['nameid'];
				$lesson_name = $line['lesson'].'. Stunde';
				$lesson = &$lessons[$nameid][$lesson_name];
				if (isset($lesson)) {
					$lesson_id = $lesson['lesson_id'];
					$info = $line['info'];
					$insert = "vertretung
                     (sid, datum, status) VALUES
                     ('$lesson_id','$this->date','$info')";
					$db->insert($insert);
				}
			}
		}
	}

	function import_teachers() {
		$nameids = $this->nameids;
		$todo = new todo();
		$todo->data['deadline'] = $this->date;
		$todo->data['expire'] = 1;
		foreach ($this->line as $i => $line) {
			if (!isset($line['sort'])) continue;
			if ($line['sort'] != 'teachers') continue;
			if ($line['class'] != 'lesson') continue;
			if (!isset($line['info'])) continue;
			$nameid = $line['headline'];
			if (!isset($nameids[$nameid])) continue;
			$todo->data['pid'] = $nameids[$nameid];
			$todo->data['text'] = $line['info'];
			$info_array = explode(' ',$line['info']);
			if (isset($info_array[0])) {
				$name = $info_array[0];
				if (isset($info_array[1])) {
					$name.= ' '.$info_array[1];
				}
			}
			$todo->data['name'] = $name;
			$todo->update();
		}
	}

	function load_schedule() {
		$query = 'text from '.$this->table.' where date="'.$this->date.'"';
		global $db;
		$db->select($query);
		if ($db->num_rows == 1) {
			$this->text = $db->data[0]['text'];
			return true;
		}
		else {
			return false;
		}
	}

	function mail($verbose=0) {
		$timestamp = strtotime('+1 day');
		$this->date = date('Y-m-d',$timestamp);
		if (!$this->load_schedule()) {
			return false;
		}
		global $db;
		$select = "CONCAT(first_name,' ',last_name) as name, mail
                   FROM person
                   WHERE opt&32=32";
		$db->select($select);
		$recipients = $db->data;
		$mail = new mail();
		$subject_v['date'] = local_date($this->date);
		$subject = new tmpl('mail_subject.txt',$subject_v);
		$mail->set_subject($subject->fdata);
		$mail->data_main = $this->text;
		$output = '';
		foreach ($recipients as $i => $person) {
			$name = $person['name'];
			$mail->sendto($person['mail'],$name);
			if ($mail->sent) $output.= '_';
			else $output.= 'X';
		}
		if ($verbose) echo $output;
		return $output;
	}

}
?>
