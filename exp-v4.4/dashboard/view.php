<?php

include_once '../config/database.php';
include_once  '../objects/dashboard.php';
include_once  '../objects/admin.php';

$database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$dashboard = new Dashboard($db);
$admin = new Admin($db);

$logs_stmt = $admin->fetch_logs();

$dataPoints = array(
	array("label" => "Expired", "y" => $dashboard->count_total_expired_client()),
	array("label" => "Mobile", "y" => $dashboard->expired_mobile()),
	array("label" => "Cash", "y" => $dashboard->expired_cash()),
);

?>
<!DOCTYPE HTML>
<html>
<style>
	table {
		border-collapse: collapse;
		border-spacing: 0;
		width: 100%;
		border: 1px solid #ddd;
	}

	th,
	td {
		text-align: left;
		padding: 8px;
	}

	tr:nth-child(even) {
		background-color: #f2f2f2
	}
</style>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script>
		window.onload = function() {

			var chart = new CanvasJS.Chart("chartContainer", {
				animationEnabled: true,
				exportEnabled: true,
				title: {
					text: "Bill Expire Status"
				},
				subtitles: [{
					text: ""
				}],
				data: [{
					type: "pie",
					showInLegend: "true",
					legendText: "{label}",
					indexLabelFontSize: 16,
					indexLabel: "{label} - {y}",
					yValueFormatString: "",
					dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
				}]
			});
			chart.render();


		}
	</script>
</head>

<body>
	<div id="chartContainer" style="height: 370px; width: 100%;"></div>
	<br>
	<strong>Recent 5 log items</strong>
	<div style="overflow-x: auto;">
		<table>
			<tr>
				<th>SL</th>
				<th>Time</th>
				<th>Details</th>
			</tr>
			<?php
			foreach ($logs_stmt as $logs) { ?>
				<tr>
					<td><?php echo $logs['id'] ?></td>
					<td><?php echo $logs['time'] ?></td>
					<td><?php echo $logs['details'] ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>

</html>