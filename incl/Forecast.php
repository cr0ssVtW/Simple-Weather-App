<?php
	require 'DarkSky.php';
	$gkey = "Google API Key Here";
	$dark_sky = new DarkSky('Dark Sky API Key Here');

	// Lets determine the location here, and get lat/long from Google Geocode
	$location = !empty($_REQUEST['location']) ? urlencode($_REQUEST['location']) : urlencode("Phoenix AZ");
	
	$url = "https://maps.googleapis.com/maps/api/geocode/json?key=$gkey&address=$location";
	$googleResponse = @file_get_contents($url);
	if ($googleResponse !== false AND !empty($googleResponse)) {
		$result = json_decode($googleResponse, true);
		$formattedLocation = $result['results'][0]['formatted_address'];
		$lat = $result['results'][0]['geometry']['location']['lat']; // '33.4483333';
		$long = $result['results'][0]['geometry']['location']['lng']; // '-112.0733333';
		if ($forecastRequest = $dark_sky->getTimeMachineRequest($lat, $long, null)) {
			$urlRequests = [$forecastRequest];
			// Get the upcoming forecast and time machine requests
			if (!empty($_REQUEST['tdp'])) {
				$datePicked = $_REQUEST['tdp'];
				if ($datePicked) {
					$unixDate = strtotime($datePicked);
					
					$timeMachineRequest = $dark_sky->getTimeMachineRequest($lat, $long, $unixDate);
					array_push($urlRequests, $timeMachineRequest);
				}
			}
			
			if ($responses = $dark_sky->makeRequests($urlRequests)) {
				if (count($responses) > 1) {
					$previousDate = $responses[1];
					$forecast = $responses[0];
				} else {
					$forecast = $responses[0];
				}

				$tz = new DateTimeZone($forecast->timezone);
				$location = !empty($_REQUEST['location']) ? urldecode($_REQUEST['location']) : urldecode("Phoenix AZ");
			}
		} else {
			echo("Unable to receive forecast from disclosed location. Try again.");
		}
	} else {
		echo("Could not communicate with Google Geocoding. Please try again,");
	}

?>