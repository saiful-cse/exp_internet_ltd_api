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
$packagesStmt = $dashboard->packages();
$bKashCollection = $dashboard->bKashCollection();


$expiredDataPoints = array(
	array("label" => "Expired", "y" => $dashboard->count_total_expired_client()),
	array("label" => "Mobile", "y" => $dashboard->expired_mobile()),
	array("label" => "Cash", "y" => $dashboard->expired_cash()),
);


while ($row = $bKashCollection->fetch(PDO::FETCH_ASSOC)) {
	$bKashdataPoints[] = $row;
}


?>
<!DOCTYPE HTML>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
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
			var expiredcChart = new CanvasJS.Chart("expiredchartContainer", {
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
					dataPoints: <?php echo json_encode($expiredDataPoints, JSON_NUMERIC_CHECK); ?>
				}]
			});
			expiredcChart.render();


			var bKashChart = new CanvasJS.Chart("bkashchartContainer", {
				animationEnabled: true,
				title: {
					text: "bKash Collection"
				},
				axisY: {
					title: "Revenue (in BDT)",
					includeZero: true,
					prefix: "৳",
					suffix: ""
				},
				data: [{
					type: "bar",
					yValueFormatString: "৳#,##0",
					indexLabel: "{y}",
					indexLabelPlacement: "inside",
					indexLabelFontWeight: "bolder",
					indexLabelFontColor: "white",
					dataPoints: <?php echo json_encode($bKashdataPoints, JSON_NUMERIC_CHECK); ?>
				}]
			});
			bKashChart.render();
		}
	</script>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>

	<!--<div class="card text-center">-->
	<!--	<div class="card-header">-->
	<!--		Notice-->
	<!--	</div>-->
	<!--	<div class="card-body">-->

	<!--		<p class="card-text">---<br>-->
			
	<!--		</p>-->

	<!--	</div>-->

	<!--</div>-->
	<!--<br>-->

	<div id="expiredchartContainer" style="height: 370px; width: 100%;"></div> <br>
	<div id="bkashchartContainer" style="height: 370px; width: 100%;"></div>

	<br>

	
	<div style="overflow-x: auto;">

	<strong>Package List</strong>
		<table>
			<tr>
				<th>Package ID</th>
				<th>Title</th>
				<th>Speed</th>
				<th>Price</th>
			</tr>
			<?php
			foreach ($packagesStmt as $pkg) { ?>
				<tr>
					<td><?php echo $pkg['pkg_id'] ?></td>
					<td><?php echo $pkg['title'] ?></td>
					<td><?php echo $pkg['speed'] ?></td>
					<td><?php echo $pkg['price']." TK" ?></td>
				</tr>
			<?php } ?>
		</table>
		<br>
		<strong>Recent login details</strong>
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
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>

</html>