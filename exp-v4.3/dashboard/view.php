<?php
 
 include_once '../config/database.php';
 include_once  '../objects/dashboard.php';


 $database = new Database();
$db = $database->getConnection();

/*
 * Initialize object
 */
$dashboard = new Dashboard($db);

$dataPoints = array(
	array("label"=> "Expired", "y"=> $dashboard->count_total_expired_client()),
	array("label"=> "Mobile", "y"=> $dashboard->expired_mobile()),
	array("label"=> "Cash", "y"=> $dashboard->expired_cash()),
);
	
?>
<!DOCTYPE HTML>
<html>
<head>  
<script>
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	exportEnabled: true,
	title:{
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
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html> 