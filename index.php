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
	<img src="caution.gif">
	<h1>ECEN 403 - Group 22's Project Website</h1>
	<div class="nodeTables">
		<div class="nodeTitle">
			All Nodes
		</div>
		<table>
			<tr>
				<th>Node ID</th>
				<th>Date Added</th>
			</tr>
		<?php
		// show table of the nodes
		foreach ($nodes as $node) {
			$nodeId = $node[0];
			$dateAdded = date_create($node[1]);?>
			<tr>
				<td><?php echo $nodeId; ?></td>
				<td><?php echo date_format($dateAdded, "F jS g:i A"); ?></td>
			</tr>
		<?php
		}
		?>
		</table>
	</div>
	<?php
	// for each node, get all the readings and organize it into various tables
	foreach ($nodes as $node) {
		$nodeId = $node[0];
		// get all readings for that node ID
		$getNodeReadings = $db->prepare("SELECT * FROM readings WHERE Node_ID = " . $nodeId);
		$getNodeReadings->execute();
		$nodeReadings = $getNodeReadings->fetchAll();
		?>
		<div class="nodeTables">
			<div class="nodeTitle">
				Node <?php echo $nodeId; ?>
			</div>
			<table class="tempTable">
				<tr>
					<th>Temperature</th>
					<th>Time of Reading</th>
				</tr>
				<?php $labels = "[";
				$data = "[";
				foreach ($nodeReadings as $reading) {
					$temperature = $reading[1];
					$time = date_create($reading[6]);
					$labels .= "'" . date_format($time, "g:i A") . "',";
					$data .= "'" . $temperature . "',";?>
					<tr>
						<td><?php echo $temperature; ?></td>
						<td><?php echo date_format($time, "F jS g:i A"); ?></td>
					</tr>
				<?php } 
				$labels = substr_replace($labels, "]", -1);
				$data = substr_replace($data, "]", -1); ?>
			</table>
			<canvas id="chart<?php echo $nodeId; ?>-temp" width="600" height="400"></canvas>
			<script>
				var ctx = $('#chart<?php echo $nodeId; ?>-temp').get(0).getContext('2d');
				var data = {
					labels : <?php echo $labels; ?>,
					datasets : [ {
						fillColor : 'rgba(151, 187, 205, 0.5)',
						strokeColor : 'rgba(151, 187, 205, 1.0)',
						pointColor : 'rgba(151, 187, 205, 1.0)',
						pointStrokeColor : '#ffffff',
						data : <?php echo $data; ?>
					} ]
				}
				var myNewChart = new Chart(ctx).Line(data);
			</script>
			<table class="locationTable">
				<tr>
					<th>Latitude</th>
					<th>Longitude</th>
					<th>Time of Reading</th>
				</tr>
				<?php foreach ($nodeReadings as $reading) {
					$latitude = $reading[2];
					$longitude = $reading[3];
					$time = date_create($reading[6]);?>
					<tr>
						<td><?php echo $latitude; ?></td>
						<td><?php echo $longitude; ?></td>
						<td><?php echo date_format($time, "F jS g:i A"); ?></td>
					</tr>
				<?php } ?>
			</table>
			<iframe 
				width="600"
				height="450"
				frameborder="0" style="border:0"
				src="https://www.google.com/maps/embed/v1/view?key=AIzaSyAkYqA8ugVtT12DgY2iuqNmAgEixj2ic40&center=<?php echo $latitude; ?>,<?php echo $longitude; ?>&zoom=17&maptype=satellite">
			</iframe>
			<table class="radiationTable">
				<tr>
					<th>Radiation</th>
					<th>Time of Reading</th>
				</tr>
				<?php $labels = "[";
				$data = "[";
				foreach ($nodeReadings as $reading) {
					$radiation = $reading[4];
					$time = date_create($reading[6]);
					$labels .= "'" . date_format($time, "g:i A") . "',";
					$data .= "'" . $radiation . "',";?>
					<tr>
						<td><?php echo $radiation; ?></td>
						<td><?php echo date_format($time, "F jS g:i A"); ?></td>
					</tr>
				<?php } 
				$labels = substr_replace($labels, "]", -1);
				$data = substr_replace($data, "]", -1); ?>
			</table>
			<canvas id="chart<?php echo $nodeId; ?>-radiation" width="600" height="400"></canvas>
			<script>
				var ctx = $('#chart<?php echo $nodeId; ?>-radiation').get(0).getContext('2d');
				var data = {
					labels : <?php echo $labels; ?>,
					datasets : [ {
						fillColor : 'rgba(151, 187, 205, 0.5)',
						strokeColor : 'rgba(151, 187, 205, 1.0)',
						pointColor : 'rgba(151, 187, 205, 1.0)',
						pointStrokeColor : '#ffffff',
						data : <?php echo $data; ?>
					} ]
				}
				var myNewChart = new Chart(ctx).Line(data);
			</script>
			<table class="carbonDioxideTable">
				<tr>
					<th>Carbon Dioxide</th>
					<th>Time of Reading</th>
				</tr>
				<?php $labels = "[";
				$data = "[";
				foreach ($nodeReadings as $reading) {
					$carbonDioxide = $reading[5];
					$time = date_create($reading[6]);
					$labels .= "'" . date_format($time, "g:i A") . "',";
					$data .= "'" . $carbonDioxide . "',";?>
					<tr>
						<td><?php echo $carbonDioxide; ?></td>
						<td><?php echo date_format($time, "F jS g:i A"); ?></td>
					</tr>
				<?php } 
				$labels = substr_replace($labels, "]", -1);
				$data = substr_replace($data, "]", -1); ?>
			</table>
			<canvas id="chart<?php echo $nodeId; ?>-co2" width="600" height="400"></canvas>
			<script>
				var ctx = $('#chart<?php echo $nodeId; ?>-co2').get(0).getContext('2d');
				var data = {
					labels : <?php echo $labels; ?>,
					datasets : [ {
						fillColor : 'rgba(151, 187, 205, 0.5)',
						strokeColor : 'rgba(151, 187, 205, 1.0)',
						pointColor : 'rgba(151, 187, 205, 1.0)',
						pointStrokeColor : '#ffffff',
						data : <?php echo $data; ?>
					} ]
				}
				var myNewChart = new Chart(ctx).Line(data);
			</script>
		</div>
	<?php
	}
	?>
</body>
</html>
