<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2008 Maikel Linke
 */

class Date {

	private $year = '0000';
	private $month = '01';
	private $day = '01';

	public function __construct($year, $month, $day) {
		$this->setYear($year);
		$this->setMonth($month);
		$this->setDay($day);
	}

	/**
	 * Returns a date in standard format.
	 *
	 * @return string
	 */
	public function toString() {
		return $this->year.'-'.$this->month.'-'.$this->day;
	}

	private function setYear($year) {
		if (checkdate($this->month, $this->day, $year)) {
			$this->year = $year;
		}
	}

	private function setMonth($month) {
		if (checkdate($month, $this->day, $this->year)) {
			$this->month = $month;
		}
	}

	private function setDay($day) {
		if (checkdate($this->month, $day, $this->year)) {
			$this->day = $day;
		}
		else {
			if ($day > 28) {
				$timestamp = strtotime($this->toString());
				$this->day = date('t', $timestamp); // t: Number of days in the given month
			}
		}
	}
}
?>