<?php

// open database
$db = new PDO('sqlite:database.sqlite') or die("cannot open the database");

// get all nodes
$getNodes = $db->prepare("SELECT * FROM nodes");
$getNodes->execute();
$nodes = $getNodes->fetchAll();
?>

<style>
	body {
		text-align:center;
		width:700px;
		margin-left:auto;
		margin-right:auto;
		font-family:Arial;
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
</style>


<img src="caution.gif">
<h1>ECEN 403 - Group 22's Project Website</h1>
<img src="underconstruction.gif">
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
		$dateAdded = $node[1];?>
		<tr>
			<td><?php echo $nodeId; ?></td>
			<td><?php echo $dateAdded; ?></td>
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
			<?php foreach ($nodeReadings as $reading) {
				$temperature = $reading[1];
				$time = $reading[6];?>
				<tr>
					<td><?php echo $temperature; ?></td>
					<td><?php echo $time; ?></td>
				</tr>
			<?php } ?>
		</table>
		<table class="locationTable">
			<tr>
				<th>Latitude</th>
				<th>Longitude</th>
				<th>Time of Reading</th>
			</tr>
			<?php foreach ($nodeReadings as $reading) {
				$latitude = $reading[2];
				$longitude = $reading[3];
				$time = $reading[6];?>
				<tr>
					<td><?php echo $latitude; ?></td>
					<td><?php echo $longitude; ?></td>
					<td><?php echo $time; ?></td>
				</tr>
			<?php } ?>
		</table>
		<table class="radiationTable">
			<tr>
				<th>Radiation</th>
				<th>Time of Reading</th>
			</tr>
			<?php foreach ($nodeReadings as $reading) {
				$radiation = $reading[4];
				$time = $reading[6];?>
				<tr>
					<td><?php echo $radiation; ?></td>
					<td><?php echo $time; ?></td>
				</tr>
			<?php } ?>
		</table>
		<table class="carbonDioxideTable">
			<tr>
				<th>Carbon Dioxide</th>
				<th>Time of Reading</th>
			</tr>
			<?php foreach ($nodeReadings as $reading) {
				$carbonDioxide = $reading[5];
				$time = $reading[6];?>
				<tr>
					<td><?php echo $carbonDioxide; ?></td>
					<td><?php echo $time; ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>
<?php
}
?>
