<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2008 Maikel Linke
 */

require_once 'class_file.php';

/*
 * Responsibilities
 *  - data holding
 *  - checking if date is new / urgent
 *  - data depending template selection
 *  - localization
 *
 * Collaborators
 *  - tmpl
 *  - DateTime
 */
class DateTimeFormatter {

	private $dateTimeString;
	private $date;
	private $time;
	private $dateTimeArray;
	private $dateTime;

	public function __construct($dateTimeString) {
		$this->dateTimeString = $dateTimeString;
		$this->dateTimeArray = dt2array($dateTimeString);
		list($this->date, $this->time) = split(' ', $dateTimeString);
		$this->dateTime = new DateTime($dateTimeString);
	}
	
	public function toStringUrgent() {
		$tmplData['dt'] = $this->dateTimeString;
		$tmplData['date'] = $this->localize();
		if ($this->date < date('Y-m-d', strtotime('+1 days'))) {
			$tmplData['class'] = 'urgent_date';
		}
		else {
			$tmplData['class'] = 'date';
		}
		$tmpl = new tmpl('date.html', $tmplData, $GLOBALS['root']);
		return (String) $tmpl->fdata;
	}

	// sorts information of a datetime-string in an array
	private function dt2array($dt) {
		$a['Y'] = substr($dt,0,4);
		$a['y'] = substr($dt,2,2);
		$a['m'] = substr($dt,5,2);
		$a['d'] = substr($dt,8,2);
		$a['H'] = substr($dt,11,2);
		$a['i'] = substr($dt,14,2);
		$a['s'] = substr($dt,17,2);
		return $a;
	}
	
	private function localize() {
		$tmplName = 'date_ymdHi.tmpl';
		$tmpl = new tmpl($tmplName,$this->dateTimeArray,'../'.path_lang());
		return $tmpl->fdata;
	}
	
}

?>