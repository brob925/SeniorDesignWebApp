<!DOCTYPE html>
<html>
<head>
	<script src="Chart.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
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
			margin-top:10px;
			margin-left:100px;
		}
		iframe {
			margin-top:10px;
			margin-left:100px;
		}
	</style>
	<script>
	function testDivClick(nodeId) {
		var xmlhttp;
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				$('#responseDiv').html('<canvas id="chart" width="600" height="400"></canvas>');
				var responseArray = xmlhttp.responseText.split(';');
				var responseLabels = responseArray[0].split(',');
				var responseData = responseArray[1].split(',');
				var response = responseArray[0].split(',');
				var ctx = $('#chart').get(0).getContext('2d');
				var data = {
					labels : responseLabels,
					datasets : [ {
						fillColor : 'rgba(151, 187, 205, 0.5)',
						strokeColor : 'rgba(151, 187, 205, 1.0)',
						pointColor : 'rgba(151, 187, 205, 1.0)',
						pointStrokeColor : '#ffffff',
						data : responseData
					} ]
				}
				var chart = new Chart(ctx).Line(data);
			}
		}
		xmlhttp.open("GET","getTemps.php?nodeid="+nodeId,true);
		xmlhttp.send();
	}
	</script>
</head>
<?php
	ini_set('display_errors', 1);
	error_reporting(~0);

	date_default_timezone_set("America/Chicago");

	// open database
	$db = new PDO("sqlite:database.sqlite") or die("cannot open the database");

	// get all nodes
	$getNodes = $db->prepare("SELECT * FROM nodes");
	$getNodes->execute();
	$nodes = $getNodes->fetchAll();
?>

<body>
	<?php foreach($nodes as $node) { ?>
		<div id="testDiv" style="width:100px;height:20px;background-color:red;" onclick="testDivClick(<?php echo $node[0]; ?>)"><?php echo $node[0]; ?></div>
	<?php } ?>
	<div id="responseDiv"></div>
</body>