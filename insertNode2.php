<?php

// get URL query string
$nodeId = $_GET['nodeid'];

// create/open database
$db = new PDO('sqlite:database2.sqlite') or die("cannot open the database");

// create node table if it doesn't exsist
$createNodeTable = "CREATE TABLE IF NOT EXISTS nodes (".
   "Node_ID TEXT PRIMARY KEY, ".
   "Created DATE DEFAULT CURRENT_TIMESTAMP".
   ")";
$db->exec($createNodeTable);

$insertIntoNodeTable = "INSERT INTO nodes(Node_ID)".
	"VALUES (" . $nodeId . ")";
$db->exec($insertIntoNodeTable);

$getNodeTable = "SELECT * FROM nodes";
$nodeTable = $db->query($getNodeTable);
?>

<style>
	body {
		text-align:center;
		width:600px;
		margin-left:auto;
		margin-right:auto;
	}
	table {
		width:90%;
	}
	table, td, th {
		border:1px solid black;
	}
</style>

<table>
<tr>
	<th>Node ID</th>
	<th>Date Created</th>
</tr>
<?php
foreach ($nodeTable as $row) {?>
	<tr>
		<td><?php echo $row[0]; ?></td>
		<td><?php echo $row[1]; ?></td>
	</tr>
<?php
}
?>
</table>