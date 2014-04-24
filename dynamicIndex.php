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
	<script src="boxIt.js"></script>
	<script src="moment.js"></script>
	<link rel="stylesheet" href="boxIt.css">
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
	<style>
		body {
			text-align:center;
			width:100%;
			margin-left:auto;
			margin-right:auto;
			margin-top:0px;
			margin-bottom:0px;
			padding-top:0px;
			font-family:Helvetica, Arial;
			background-color:#E0E0E0;
			overflow-y:hidden;
		}
		#chartWrapper {
			background-color:white;
			width:100%;
			height:auto;
		}
		#chart {
			border-top:1px solid white;
			border-left:1px solid white;
			border-right:1px solid white;
			border-bottom:none;
			margin-left:auto;
			margin-right:auto;
		}
		.selectWrapper {
			background-color:lightslategray;
			width:100%;
			height:auto;
		}
		#selectors {
			text-align:left;
			background-color:lightslategray;
			width:1080px;
			height:40px;
			padding-top:10px;
			margin-left:auto;
			margin-right:auto;
		}
		.selectboxit-container {
			margin-top:-1px;
			margin-left:140px;
		}
		.selectboxit-container .selectboxit-options {
			width:141px;
		}
		#chart table{
			border-collapse: collapse;
			border-spacing: 0;
			width:100%;
			height:100%;
			margin:0px;padding:0px;
		}
		#chart tr:last-child td:last-child {
			-moz-border-radius-bottomright:0px;
			-webkit-border-bottom-right-radius:0px;
			border-bottom-right-radius:0px;
		}
		#chart table tr:first-child td:first-child {
			-moz-border-radius-topleft:0px;
			-webkit-border-top-left-radius:0px;
			border-top-left-radius:0px;
		}
		#chart table tr:first-child td:last-child {
			-moz-border-radius-topright:0px;
			-webkit-border-top-right-radius:0px;
			border-top-right-radius:0px;
		}
		#chart tr:last-child td:first-child{
			-moz-border-radius-bottomleft:0px;
			-webkit-border-bottom-left-radius:0px;
			border-bottom-left-radius:0px;
		}
		#chart tr:hover td{
			background-color:lightslategray;
		}
		#chart tr:nth-child(odd) { background-color:#aad4ff; }
		#chart tr:nth-child(even) { background-color:#ffffff; }
		#chart td{
			vertical-align:middle;	
			border:1px solid #ffffff;
			border-width:0px 1px 1px 0px;
			text-align:left;
			padding:7px;
			font-size:14px;
			font-family:Arial;
			font-weight:normal;
			color:#000000;
		}
		#chart tr:last-child td{
			border-width:0px 1px 0px 0px;
		}
		#chart tr td:last-child{
			border-width:0px 0px 1px 0px;
		}
		#chart tr:last-child td:last-child{
			border-width:0px 0px 0px 0px;
		}
		#chart tr:first-child td{
			background:-o-linear-gradient(bottom, #005fbf 5%, #003f7f 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #005fbf), color-stop(1, #003f7f) );
			background:-moz-linear-gradient( center top, #005fbf 5%, #003f7f 100% );
			filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#005fbf", endColorstr="#003f7f");	background: -o-linear-gradient(top,#005fbf,003f7f);
			background-color:#005fbf;
			border:0px solid #ffffff;
			text-align:center;
			border-width:0px 0px 1px 1px;
			font-size:14px;
			font-family:Arial;
			font-weight:bold;
			color:#ffffff;
		}
		#chart tr:first-child:hover td{
			background:-o-linear-gradient(bottom, #005fbf 5%, #003f7f 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #005fbf), color-stop(1, #003f7f) );
			background:-moz-linear-gradient( center top, #005fbf 5%, #003f7f 100% );
			filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#005fbf", endColorstr="#003f7f");	background: -o-linear-gradient(top,#005fbf,003f7f);
			background-color:#005fbf;
		}
		#chart tr:first-child td:first-child{
			border-width:0px 0px 1px 0px;
		}
		#chart tr:first-child td:last-child{
			border-width:0px 0px 1px 1px;
		}
		.ui-datepicker {
			background-color:#E0E0E0;
			opacity:0.9;
			width:250px;
			height:auto;
			font-size:12px;
		}
		#dateSelect {
			background-color:lightslategray;
			width:1078px;
			height:40px;
			position:relative;
			margin-left:auto;
			margin-right:auto;
			font-size:14px;
		}
		#startDate {
			float:left;
			margin-left:140px;
			margin-top:9px;
			width:169px;
			cursor:text;
		}
		#endDate {
			float:left;
			margin-left:140px;
			margin-top:9px;
			width:169px;
			cursor:text;
		}
		#submitDate {
			float:left;
			margin-left:144px;
			margin-top:8px;
			width:173px;
		}
		.ui-button-text-only .ui-button-text {
			padding-top:2px;
			padding-bottom:2px;
			color:black;
			font-family:Helvetica, Arial;
		}
	</style>
	<script>
	google.load('visualization', '1.1', {'packages':['annotationchart']});
	google.setOnLoadCallback(drawGraphic);
	var nodeActive = <?php echo $nodes[0][0]; ?>;
	var tempActive = true;
	var locationActive = false;
	var radActive = false;
	var co2Active = false;
	var graphicActive = true;
	var tableActive = false;
	var startDate = 'null';
	var endDate = 'null';
	
	var chart;
	function drawGraphic() {
		clearChart();
		$('#chart').css('overflow-y', 'hidden');
		$('#chart').css('overflow-x', 'hidden');
		if (locationActive) {
			drawMap();
			return;
		}
		var chartHeight = $(window).height();
		var chartHeight = chartHeight-50;
		$('#chart').css('height', chartHeight+'px');
		$('#dateSelectWrapper').css('display', 'none');
		var startDate = '';
		var endDate = '';
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
		if (tempActive) {
			getURL = "getTemps.php";
		} else if (radActive) {
			getURL = "getRad.php";
		} else if (co2Active) {
			getURL = "getCo2.php";
		}
		xmlhttp.open("GET",getURL+"?nodeid="+nodeActive+'&startdate=null&enddate=null',true);
		xmlhttp.send();
	}
	
	var map;
	function drawMap() {
		var chartHeight = $(window).height();
		var chartHeight = chartHeight-90;
		$('#chart').css('height', chartHeight+'px');
		$('#dateSelectWrapper').css('display', 'block');
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
				
				for (var i=maxIndex; i>=0; i--) {
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
		xmlhttp.open("GET","getLocation.php?nodeid="+nodeActive+'&startdate='+startDate+'&enddate='+endDate,true);
		xmlhttp.send();
	}
	
	function drawTable() {
		clearChart();
		$('#chart').css('overflow-y', 'auto');
		var chartHeight = $(window).height();
		var chartHeight = chartHeight-90;
		$('#chart').css('height', chartHeight+'px');
		$('#dateSelectWrapper').css('display', 'block');
		var xmlhttp;
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				var tableHtml = '';
				var responseArray = xmlhttp.responseText.split(';');
				var dataType = responseArray[0];
				tableHtml += '<table>';
				var datetimes = responseArray[1].split(',');
				var maxIndex = datetimes.length-1;
				if (locationActive) {
					tableHtml += '<tr><th>Latitude</th><th>Longitude</th><th>Date</th></tr>';
					var latitudes = responseArray[2].split(',');
					var longitudes = responseArray[3].split(',');
					for (var i=maxIndex; i>=0; i--) {
						var dateVar = new Date(datetimes[i]);
						tableHtml += '<tr><td>'+latitudes[i]+'</td><td>'+longitudes[i]+'</td><td>'+moment(dateVar).format('h:mm A MMMM Do, YYYY')+'</td></tr>';
					}
				} else {
					tableHtml += '<tr><th>'+dataType+'</th><th>Date</th></tr>';
					var data = responseArray[2].split(',');
					for (var i=maxIndex; i>=0; i--) {
						var dateVar = new Date(datetimes[i]);
						tableHtml += '<tr><td>'+data[i]+'</td><td>'+moment(dateVar).format('h:mm A MMMM Do, YYYY')+'</td></tr>';
					}
				}
				tableHtml += '</table>';
				$('#chart').html(tableHtml);
				
			}
		}
		var getURL = "";
		if (tempActive) {
			getURL = "getTemps.php";
		} else if (locationActive) {
			getURL = "getLocation.php";
		}else if (radActive) {
			getURL = "getRad.php";
		} else if (co2Active) {
			getURL = "getCo2.php";
		}
		xmlhttp.open("GET",getURL+"?nodeid="+nodeActive+'&startdate='+startDate+'&enddate='+endDate,true);
		xmlhttp.send();
	}
	
	function tempClick() {
		tempActive = true;
		locationActive = false;
		radActive = false;
		co2Active = false;
		if (graphicActive) {
			drawGraphic();
		} else if (tableActive) {
			drawTable();
		}
	}
	
	function locationClick() {
		tempActive = false;
		locationActive = true;
		radActive = false;
		co2Active = false;
		if (graphicActive) {
			drawGraphic();
		} else if (tableActive) {
			drawTable();
		}
	}
	
	function radClick() {
		tempActive = false;
		locationActive = false;
		radActive = true;
		co2Active = false;
		if (graphicActive) {
			drawGraphic();
		} else if (tableActive) {
			drawTable();
		}
	}
	
	function co2Click() {
		tempActive = false;
		locationActive = false;
		radActive = false;
		co2Active = true;
		if (graphicActive) {
			drawGraphic();
		} else if (tableActive) {
			drawTable();
		}
	}
	
	function clearChart() {
		$('#chart').empty();
	}
	
	$(document).ready(function () {
		$('select').selectBoxIt();
		$('#startDate').datepicker();
		$('#endDate').datepicker();
		
		$('button').button();
		
		$(document).on('change', '.nodeSelect', function() {
			nodeActive = $(this[this.selectedIndex]).val();
			if (graphicActive) {
				drawGraphic();
			} else if (tableActive) {
				drawTable();
			}
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
		
		$(document).on('change', '.displaySelect', function() {
			var selection = $(this[this.selectedIndex]).val();
			if (selection == 'graphic') {
				graphicActive = true;
				tableActive = false;
				drawGraphic();
			} else if (selection == 'table') {
				graphicActive = false;
				tableActive = true;
				drawTable();
			}
		});
		
		$(document).on('change', '#startDate', function() {
			startDate = $('#startDate').datepicker('getDate');
		});
		
		$(document).on('change', '#endDate', function() {
			endDate = $('#endDate').datepicker('getDate');
		});
		
		$('#submitDate').click(function() {
			if (graphicActive) {
				drawGraphic();
			} else if (tableActive) {
				drawTable();
			}
		});
	});
	</script>
</head>

<body>
	<div class="selectWrapper">
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
			<select class="displaySelect">
				<option value="graphic">Graphic</option>
				<option value="table">Table</option>
			</select>
		</div>
	</div>
	<div id="chartWrapper">
		<div id="chart" style="width:1078px;height:495px;"></div>
	</div>
	<div class="selectWrapper" id="dateSelectWrapper">
		<div id="dateSelect">
			<input type="text" id="startDate" placeholder="Start Date" readonly="true">
			<input type="text" id="endDate" placeholder="End Date" readonly="true">
			<button id="submitDate">Submit</button>
		</div>
	</div>
</body>