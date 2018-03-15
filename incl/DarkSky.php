<?php 

class DarkSky 
{
	// version 0.1
	// author: Nick Margalis
	// github: https://github.com/cr0ssVtW

	// Set some variables for calling the API.
	const UNITS = 'auto';
	const API_URL = 'https://api.darksky.net/forecast/';
	
	private $key;

	// Create the constructor with key.
	public function __construct($key)
	{
		$this->key = $key;
	}

	// Get the Time Machine forecast/request
	// https://api.darksky.net/forecast/[key]/[latitude],[longitude],[time]
	// Returns a request to be made.
	public function getTimeMachineRequest($lat, $long, $time)
	{
		if ($time === null) {
			$request = sprintf('%s/%s,%s', $this->key, $lat, $long);	
		}
		else {
			$request = sprintf('%s/%s,%s,%s', $this->key, $lat, $long, $time);
		}
		$request = self::API_URL . $request . "?units=" . self::UNITS;
		// TODO: Add parameters (excludes) for request/constructor
		return $request;
	}

	// Make the request and return array of json requests
	public function makeRequests(array $requestURLs)
	{
		$responses = $this->processUrls($requestURLs); 
		$theArrayResponse = array();
		foreach ($responses as $responses) {
			$decoded = json_decode($responses);
			array_push($theArrayResponse, $decoded);
		}

		return $theArrayResponse;
	}

	private function processUrls(array $urls) {

		$curls = array();
		$result = array();

		// multi handle exec
		$mexec = curl_multi_init();

		// loop through $urls and create handles
		// add handles to the multi-handle exec
		foreach ($urls as $id => $d) {
			$curls[$id] = curl_init();

			$url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
			curl_setopt($curls[$id], CURLOPT_URL,            $url);
			curl_setopt($curls[$id], CURLOPT_HEADER,         0);
			curl_setopt($curls[$id], CURLOPT_RETURNTRANSFER, 1);

			curl_multi_add_handle($mexec, $curls[$id]);
		}

		// execute the handles from mexec
		$running = null;
		do {
			curl_multi_exec($mexec, $running);
		} while($running > 0);


		// get content and clean up
		foreach($curls as $id => $c) {
			$result[$id] = curl_multi_getcontent($c);
			curl_multi_remove_handle($mexec, $c);
		}

		// close it
		curl_multi_close($mexec);

		return $result;
	}

}

?>