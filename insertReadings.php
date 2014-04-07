<?php

// get URL query string
$nodeId = $_GET['nodeid'];
$temp = $_GET['temp'];
$lat = $_GET['lat'];
$long = $_GET['long'];
$radiation = $_GET['rad'];
$co2 = $_GET['co2'];

// create/open database
$db = new PDO('sqlite:database.sqlite') or die("cannot open the database");

// create readings table if it doesn't exsist
$createReadingsTable = "CREATE TABLE IF NOT EXISTS readings (".
   "Node_ID TEXT, ".
   "Temperature REAL, ".
   "Latitude REAL, ".
   "Longitude REAL, ".
   "Radiation REAL, ".
   "CO2 REAL, ".
   "Created DATE DEFAULT CURRENT_TIMESTAMP".
   ")";
$db->exec($createReadingsTable);

$insertIntoReadingsTable = "INSERT INTO readings(Node_ID, Temperature, ".
	"Latitude, Longitude, Radiation, CO2) ".
	"VALUES (" . $nodeId . ", " . $temp. ", ".
	$lat . ", " . $long. ", " . $radiation . ", " . $co2 . ")";
$db->exec($insertIntoReadingsTable);

$getReadingsTable = "SELECT * FROM readings";
$readingsTable = $db->query($getReadingsTable);
?>

<style>
	body {
		text-align:center;
		width:900px;
		margin-left:auto;
		margin-right:auto;
	}
	table {
		width:100%;
	}
	table, td, th {
		border:1px solid black;
	}
</style>

<table>
<tr>
	<th>Node ID</th>
	<th>Temperature</th>
	<th>Latitude</th>
	<th>Longitude</th>
	<th>Radiation</th>
	<th>CO2 Level</th>
	<th>Date Created</th>
</tr>
<?php
foreach ($readingsTable as $row) {?>
	<tr>
		<td><?php echo $row[0]; ?></td>
		<td><?php echo $row[1]; ?></td>
		<td><?php echo $row[2]; ?></td>
		<td><?php echo $row[3]; ?></td>
		<td><?php echo $row[4]; ?></td>
		<td><?php echo $row[5]; ?></td>
		<td><?php echo $row[6]; ?></td>
	</tr>
<?php
}
?>
</table>