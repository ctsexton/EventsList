<?php namespace CamSexton\Events\Components;

use Cms\Classes\ComponentBase;
use Db;
use DateTime;
use DateTimeZone;

class PastEvents extends ComponentBase

{
	public function componentDetails() {
		return [
			'name' => 'Past Events',
			'description' => 'Display past events from database'
		];
	}

	public function onRun() {
		$this->queryEvents();
	}

	public $events;

	protected function queryEvents() {
		$this->events = Db::select("SELECT 
			title, 
			venue,
			date_time,
			event_timezone,
			description,
			file_id
			FROM camsexton_events_entries
			WHERE date_time < datetime('now')
			ORDER BY date_time DESC
			");

		if ($this->events == null) {
			$noEvents = array(
			"title" => "No Past Events",
			"venue" => "",
			"format_date" => "",
			"description" => "");
			$this->events = [$noEvents];
			return;
		}

		foreach ($this->events as $item) {
			// Change date timezone from UTC to event timezone
			$date = $item->date_time;
			$format_date = new DateTime($date);
			$tz = new DateTimeZone($item->event_timezone);
			$format_date->setTimezone($tz);
			$item->format_date = $format_date->format('l j F Y \a\t g:ia');
			$item->description = htmlspecialchars_decode($item->description);
			if ($item->file_id != "") {
				$file_id = $item->file_id;
				$item->photo_url = 'storage/app/media/' . $file_id . ".jpeg";
			} else {
				$item->photo_url = "";
			}
		}
	}
}
