<?php
session_start();
date_default_timezone_set("Asia/Dhaka");
include_once '../config/database.php';
include_once  '../objects/dashboard.php';
include_once  '../objects/admin.php';
include_once  '../objects/sms.php';

$database = new Database();
$db = $database->getConnection();

$_SESSION['msg'] = "";

/*
 * Initialize object
 */
$dashboard = new Dashboard($db);
$admin = new Admin($db);
$logs_stmt = $admin->fetch_logs();
$packagesStmt = $dashboard->packages();
$bKashCollection = $dashboard->bKashCollection();


function expired_client_sms()
{

	$sms_message = "⚠️ Warning!! 
আপনার Wi-Fi সংযোগের মেয়াদ আগামী ৩ দিন পর শেষ হবে। সংযোগটি সচল রাখতে বিল পরিশোধ করুন।
https://baycombd.com/paybill/
01975-559161 (bKash Payment)";

	$database = new Database();
	$db = $database->getConnection();
	$sms = new Sms($db);

	$sms->current_date = date("Y-m-d H:i:s");

	//getting client before 3day expire
	$stmt = $sms->getExpiredClientsPhone();
	$data = $stmt->rowCount();

	if ($data > 0) {

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			$num[] = $row['phone'];
		}
		$numbers =  implode(', ', $num);

		//Set the value
		$sms->numbers = $numbers;
		$sms->msg_body = $sms_message;
		$sms->created_at = date("Y-m-d H:i:s");

		//SMS service
		$url = "http://66.45.237.70/api.php";
		$data = array(
			'username' => "01835559161",
			'password' => "saiful@#21490",
			'number' => $numbers,
			'message' => $sms_message
		);

		$ch = curl_init(); // Initialize cURL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$smsresult = curl_exec($ch);

		$p = explode("|", $smsresult);
		$sendstatus = $p[0];

		switch ($sendstatus) {
			case '1000':
				$_SESSION['msg']  = "Invalid user or Password";
				break;
			case '1002':
				$_SESSION['msg'] = "Empty Number";
				break;
			case '1003':
				$_SESSION['msg'] = "Invalid message or empty message";
				break;
			case '1004':
				$_SESSION['msg'] = "Invalid number";
				break;
			case '1005':
				$_SESSION['msg'] = "All Number is Invalid";
				break;
			case '1006':

				$_SESSION['msg'] = '<div class="alert alert-danger" role="alert">Insufficient Balance</div>';

				break;

			case '1009':

				$_SESSION['msg'] = "Inactive Account, contact with software developer.";

				break;

			case '1010':

				$_SESSION['msg'] = "Max number limit exceeded";

				break;

			case '1101':

				if ($sms->expiredClientSmsStoreUpdate()) {
					$_SESSION['msg'] =
						'<div class="alert alert-success" role="alert">
					SMS sent successfully
				  </div>';
				} else {

					$_SESSION['msg'] = "SMS sending error!!";
				}
				break;
		}
	} else {

		$_SESSION['msg'] =
			'<div class="alert alert-success" role="alert">
					Allready sent sms
				  </div>';
	}
}

function expired_client_disconnect()
{

	$database = new Database();
	$db = $database->getConnection();
	$sms = new Sms($db);
	
	$sms->current_date = date("Y-m-d H:i:s");

	$stmt = $sms->getExpiredClientsPhonePPPname();

	if ($stmt->rowCount() > 0) {

		//Collecting phone numbers and ppp name
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			$num[] = $row['phone'];
			$pppName[] = $row['ppp_name'];
			$id[] = $row['id'];
		}
		$numbers =  implode(', ', $num);
		$sms->id_list = implode(', ', $id);

		//Disable and Remove form mikrotik server
		$data = json_decode(pppListDisable($pppName), true);

		if ($data['status'] == 200) {

			//after success disabled, send sms
			$message = "আপনার WiFi সংযোগের মেয়াদ শেষ, পুনরায় চালু করতে বিল পরিশোধ করুন।\n https://baycombd.com/paybill/ \n01975-559161 (bKash Payment)";

			$url = "http://66.45.237.70/api.php";
			$data = array(
				'username' => "01835559161",
				'password' => "saiful@#21490",
				'number' => $numbers,
				'message' => $message
			);

			$ch = curl_init(); // Initialize cURL
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$smsresult = curl_exec($ch);

			$p = explode("|", $smsresult);
			$sendstatus = $p[0];

			switch ($sendstatus) {
				case '1000':
					echo json_encode(array("message" => "Invalid user or Password"));
					break;
				case '1002':
					echo json_encode(array("message" => "Empty Number"));
					break;
				case '1003':
					echo json_encode(array("message" => "Invalid message or empty message"));
					break;
				case '1004':
					echo json_encode(array("message" => "Invalid number"));
					break;
				case '1005':
					echo json_encode(array("message" => "All Number is Invalid"));
					break;
				case '1006':

					echo json_encode(array(
						"status" => 1006,
						"message" => "Insufficient Balance"

					));
					break;

				case '1009':

					echo json_encode(array(
						"status" => 1009,
						"message" => "Inactive Account, contact with software developer."

					));
					break;

				case '1010':

					echo json_encode(array(
						"status" => 1010,
						"message" => "Max number limit exceeded"

					));
					break;

				case '1101':

					//Update clients mode status on DB
					if ($sms->clientDisconnectModeUpdate()) {
						echo json_encode(array(

							"status" => 200,
							"message" => "SMS sent and disconnected successfully"

						));
					} else {

						echo json_encode(array(
							"status" => 201,
							"message" => "SMS sending error!!"
						));
					}
					break;
			}
		} else {
			echo json_encode(array(
				"status" => $data['status'],
				"message" => $data['message']
			));
		}
	} else {

		echo json_encode(array(
			"status" => 404,
			"message" => "Not found expired clients in this time."
		));
	}
}


expired_client_sms();
//sms service for expired clients
if (strtotime(date("H:i:s")) >=  strtotime('09:00:00') and strtotime(date("H:i:s")) <=  strtotime('17:00:00')) {
	expired_client_sms();
}

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

	<div class="card text-center">
		<!-- <div class="card-header">
			Notice
		</div> -->
		<div class="card-body">

			<p class="card-text">
				<?php echo $_SESSION['msg']; ?>
			</p>

		</div>

	</div>
	<br>

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
					<td><?php echo $pkg['price'] . " TK" ?></td>
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