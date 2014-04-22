<?php
	ini_set('display_errors', 1);
	error_reporting(~0);

	date_default_timezone_set("America/Chicago");

	// open database
	$db = new PDO("sqlite:database2.sqlite") or die("cannot open the database");

	// get all nodes
	$getNodes = $db->prepare("SELECT * FROM nodes");
	$getNodes->execute();
	$nodes = $getNodes->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
	<script src='http://www.google.com/jsapi'></script>
	<script src='https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false'></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
	<script src="boxIt.js" type="text/javascript"></script>
	<link rel="stylesheet" href="boxIt.css">
	<style>
		html {
			background-image:url('stardust.png');
		}
		body {
			text-align:center;
			width:800px;
			margin-left:auto;
			margin-right:auto;
			margin-top:0px;
			margin-bottom:0px;
			padding:50px;
			padding-top:0px;
			font-family:Arial;
			background-color:#E0E0E0;
		}
		table {
			width:100%;
			margin-left:auto;
			margin-right:auto;
			margin-top:14px;
		}
		table, td, th {
			border:1px solid black;
		}
		.nodeTables {
			text-align:left;
			padding-top:25px;
		}
		.nodeTitle {
			font-size:24px;
			font-weight:bold;
		}
		canvas {
			float:left;
		}
		iframe {
			margin-top:10px;
			margin-left:100px;
		}
		#nodeSelector {
			float:left;
			width:100%;
			height:21px;
			text-align:center;
			background-color:white;
		}
		.nodeSelection {
			float:left;
			margin-left:1px;
			height:20px;
			background-color:gray;
			color:white;
			font-weight:bold;
			cursor:pointer;
		}
		#sensorSelector {
			float:left;
			width:199px;
			height:410px;
			background-color:gray;
			border-left:1px solid white;
		}
		.sensorSelection {
			padding-top:15px;
			padding-bottom:15px;
			border-bottom:1px solid white;
			color:white;
			font-weight:bold;
			font-size:18px;
			cursor:pointer;
		}
		#chart {
			border:1px solid white;
		}
		#selectors {
			text-align:left;
			background-color:gray;
			float:left;
			width:800px;
			height:40px;
		}
		.selectboxit-container {
			margin-top:4px;
			margin-left:151px;
		}
		.selectboxit-container .selectboxit-options {
			width:141px;
		}
	</style>
	<script>
	google.load('visualization', '1.1', {'packages':['annotationchart']});
	google.setOnLoadCallback(drawChart);
	var nodeActive = <?php echo $nodes[0][0]; ?>;
	var tempActive = true;
	var locationActive = false;
	var radActive = false;
	var co2Active = false;
	var chart;
	function drawChart() {
		clearChart();
		$('.nodeSelection').css('background-color', 'gray');
		$('#node'+nodeActive).css('background-color', 'rgba(151, 187, 205, 1.0)');
		if (locationActive) {
			drawMap();
			return;
		}
		var xmlhttp;
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				var responseArray = xmlhttp.responseText.split(';');
				var dataType = responseArray[0];
				var datetimes = responseArray[1].split(',');
				var data = responseArray[2].split(',');
				var chartData = new google.visualization.DataTable();
				chartData.addColumn('datetime', 'Date');
				chartData.addColumn('number', dataType);
				var rows = [];
				for (var i=0; i<data.length; i++) {
					rows.push([new Date(datetimes[i]), Number(data[i])]);
				}
				chartData.addRows(rows);
				chart = new google.visualization.AnnotationChart(document.getElementById('chart'));
				if (data.length > 25) {
					var options = {
						fill:20,
						zoomEndTime:new Date(datetimes[data.length-1]),
						zoomStartTime:new Date(datetimes[data.length-25]),
					};
				} else {
					var options = {
						fill:20,
					};
				}
				chart.draw(chartData, options);
			}
		}
		var getURL = "";
		$('.sensorSelection').css('background-color', 'gray');
		if (tempActive) {
			getURL = "getTemps.php";
			$('#temperature').css('background-color', 'rgba(151, 187, 205, 1.0)');
		} else if (radActive) {
			getURL = "getRad.php";
			$('#radiation').css('background-color', 'rgba(151, 187, 205, 1.0)');
		} else if (co2Active) {
			getURL = "getCo2.php";
			$('#carbonDioxide').css('background-color', 'rgba(151, 187, 205, 1.0)');
		}
		xmlhttp.open("GET",getURL+"?nodeid="+nodeActive,true);
		xmlhttp.send();
	}
	
	var map;
	function drawMap() {
		$('.sensorSelection').css('background-color', 'gray');
		$('#location').css('background-color', 'rgba(151, 187, 205, 1.0)');
		var xmlhttp;
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				var responseArray = xmlhttp.responseText.split(';');
				var dataType = responseArray[0];
				var datetimes = responseArray[1].split(',');
				var latitudes = responseArray[2].split(',');
				var longitudes = responseArray[3].split(',');
				var maxIndex = datetimes.length-1;
				var mapOptions = {
					zoom: 14,
					center: new google.maps.LatLng(latitudes[maxIndex], longitudes[maxIndex])
				};
				map = new google.maps.Map(document.getElementById('chart'),
					mapOptions);
				
				for (var i=maxIndex; i>maxIndex-5; i--) {
					var contentString = 'Latitude: ' + latitudes[i] + '<br>' +
						'Longitude: ' + longitudes[i] + '<br>' +
						'Date and Time: ' + datetimes[i];
					var infowindow = new google.maps.InfoWindow({
						content: contentString,
						maxWidth: 200
					});
					
					var marker = new google.maps.Marker({
						position: new google.maps.LatLng(latitudes[i], longitudes[i]),
						map: map,
						title: datetimes[i]
					});
					
					google.maps.event.addListener(marker, 'click', function() {
						infowindow.open(map,marker);
					});
				}
			}
		}
		xmlhttp.open("GET","getLocation.php?nodeid="+nodeActive,true);
		xmlhttp.send();
	}
	
	function tempClick() {
		if (tempActive) return;
		tempActive = true;
		locationActive = false;
		radActive = false;
		co2Active = false;
		drawChart();
	}
	
	function locationClick() {
		if (locationActive) return;
		tempActive = false;
		locationActive = true;
		radActive = false;
		co2Active = false;
		drawChart();
	}
	
	function radClick() {
		if (radActive) return;
		tempActive = false;
		locationActive = false;
		radActive = true;
		co2Active = false;
		drawChart();
	}
	
	function co2Click() {
		if (co2Active) return;
		tempActive = false;
		locationActive = false;
		radActive = false;
		co2Active = true;
		drawChart();
	}
	
	function clearChart() {
		$('#chart').empty();
	}
	
	$(document).ready(function () {
		$('select').selectBoxIt();
		
		$(document).on('change', '.nodeSelect', function() {
			nodeActive = $(this[this.selectedIndex]).val();
			drawChart();
		});
		
		$(document).on('change', '.sensorSelect', function() {
			var selection = $(this[this.selectedIndex]).val();
			if (selection == 'temperature') {
				tempClick();
			} else if (selection == 'location') {
				locationClick();
			} else if (selection == 'radiation') {
				radClick();
			} else if (selection == 'carbonDioxide') {
				co2Click();
			}
		});
	});
	</script>
</head>

<body>
	<img src="caution.gif">
	<h1>ECEN 403 - Group 22's Project Website</h1>
	<div id="selectors">
		<select class="nodeSelect">
			<?php foreach($nodes as $node) {?>
				<option value="<?php echo $node[0]; ?>">Node <?php echo $node[0]; ?></option>
			<?php } ?>
		</select>
		<select class="sensorSelect">
			<option value="temperature">Temperature</option>
			<option value="location">Location</option>
			<option value="radiation">Radiation</option>
			<option value="carbonDioxide">Carbon Dioxide</option>
		</select>
	</div>
	<div id="#responseDiv"><div id="chart" style="width:798px;height:494px;float:left;"></div></div>
	<br clear="all">
</body>