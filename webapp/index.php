<?php
	ini_set('display_errors', 1);
	error_reporting(~0);

	date_default_timezone_set("America/Chicago");

	// open database
	$db = new PDO("sqlite:../database2.sqlite") or die("cannot open the database");

	// get all nodes
	$getNodes = $db->prepare("SELECT * FROM nodes");
	$getNodes->execute();
	$nodes = $getNodes->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="description" content="ECEN 403 Team 22 Project">
	<meta name="author" content="Blake Robertson">
	<title>WSN Web App</title>
	<link rel="shortcut icon" href="../images/favicon.ico">
	<script src='http://www.google.com/jsapi'></script>
	<script src='https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false'></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
	<script src="../js/boxIt.js"></script>
	<script src="../js/moment.js"></script>
	<link rel="stylesheet" href="../css/boxIt.css">
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
	<link rel="stylesheet" href="../css/appStyles.css">
	<script src="../js/javascript.js"></script>
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