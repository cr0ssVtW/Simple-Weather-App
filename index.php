<html>
<head>
	<title>A Simple Weather App</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-datepicker.css">
</head>
<body>
	<div class="container">
		<?php include 'incl/Forecast.php'; ?>
		<form id="weatherForm" action="index.php">
			<h1>Weekly Forecast</h1>
			<div class="row">
				<div class="col-md-4">
					<b>Location:</b>
					<div class="form-group">
						<?php
							if (!empty($formattedLocation)) {
								echo("<input type=\"text\" id=\"location\" onClick=\"this.select();\" name=\"location\" class=\"form-control form-group\" value=\"$formattedLocation\">");
							} else {
								// TODO: Ajax location. Google api?
								echo("<input type=\"text\" id=\"location\" name=\"location\" class=\"form-control form-group\" placeholder=\"Type location here. Ex: Phoenix AZ\">");
							}
						?>
						<input class="btn btn-primary btn-sm" type="submit" value="Set Location" />
					</div>
				</div>
			</div>
			<input style="position: absolute; left: -9999px; width: 1px; height: 1px;" type="submit" value="Get Weather" />
			<p></p>
			<div class="row">
				<?php
					// Display the forecast
					// TODO: Cleanup, add icons for weather & beautify the thing.
					if (isset($forecast)) {
						foreach ($forecast->daily->data as $day)
						{
							echo("<div class=\"col-sm-3\">");
							$date = new DateTime();
							$date->setTimestamp($day->time);
							$date->setTimezone($tz);
							echo("<p><b>" . $date->format('l F j Y') . "</b><br>");
							echo("<i>" . $day->summary . "</i><br>");
							echo("Precipitation: " . round($day->precipProbability * 100) . "%<br>");
							echo("Humidity: " . round($day->humidity * 100) . "%<br>");
							echo("Wind: " . round($day->windSpeed) . " kph / " . round($day->windSpeed * 0.621371) . " mph<br>");
							echo("High: " . round($day->temperatureHigh) . "° | Low: " . round($day->temperatureLow) . "°</p>");
							echo("</div>");
						}
					}
				?>
			</div>
			<p></p>
			<hr/>
			<center><h1>Time Machine</h1></center>
			<div class="row">
				<div class="col-md-4">
					<div id="dates" class="form-group date">
					  <input type="text" id="tdp"  name="tdp" data-date-format="MM/DD/YYYY" class="form-control form-group"><span class="input-group-addon btn btn-primary btn-sm">Select Date</span>
					  <input style="position: absolute; left: -9999px; width: 1px; height: 1px;" type="submit" value="Get Weather" />
					</div>
				</div>
				<?php 
					// TODO: Cleanup, add icons for weather & beautify the thing.
					if (!empty($_REQUEST['tdp'])) {
						if (isset($previousDate)) {
							$date = new DateTime();
							$date->setTimestamp($previousDate->currently->time);
							$date->setTimezone($tz);
							$i = 0;
							echo("<div class=\"col-md-4\">");
							echo("<b>" . $date->format('l F j Y') . "</b><br>");
							$theLocation = !empty($formattedLocation) ? $formattedLocation : $location;
							echo("<b><i>" . $theLocation . "</i></b><br>");
							echo("<i>" . $previousDate->currently->summary . "</i><br>");
							echo("High: " . round($previousDate->daily->data[$i]->temperatureHigh) . "° | Low: " . round($previousDate->daily->data[$i]->temperatureLow) . "°");
							echo("</div>");
							echo("<div class=\"col-md-4\">");
							echo("Precipitation: " . round($previousDate->daily->data[$i]->precipProbability * 100) . "%<br>");
							echo("Humidity: " . round($previousDate->daily->data[$i]->humidity * 100) . "%<br>");
							echo("Wind: " . round($previousDate->daily->data[$i]->windSpeed) . " kph / " . round($day->windSpeed * 0.621371) . " mph<br>");
							echo("</div>");
						}
			        } else {
		            	echo "<h4>Select a date to see its recorded weather/upcoming predictions.</h4>";
			        }
				?>
			</div>
		</form>
	</div>

	<script language="JavaScript" type="text/javascript" src="js/jquery.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="js/bootstrap.js"></script>
	<script language="JavaScript" type="text/javascript" src="js/bootstrap-datepicker.js"></script>
	<script type="text/javascript">
		$( document ).ready(function() {
			$('#dates').datepicker({
				startDate: '-30d',
				endDate: '+7d',
			  	autoclose: true
			});

			$('#dates').on('changeDate', function() {
				$('#weatherForm').submit();
			});
		});
	</script>
</body>
</html>
