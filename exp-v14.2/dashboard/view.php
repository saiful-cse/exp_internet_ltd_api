<?php
date_default_timezone_set("Asia/Dhaka");
include_once '../config/database.php';
include_once  '../objects/dashboard.php';
include_once  '../objects/employee.php';
include_once  '../objects/sms.php';
include_once  '../sms_gateway/sms_send.php';

$database = new Database();
$db = $database->getConnection();
$sms_expiring_3day_result = $sms_expired_client_disconnect_result = $expired_take_time_client_disconnect_result = "";
$zone = $_GET['zone'];

function expipering_3day_client_sms_send()
{
	include_once '../config/database.php';
	include_once  '../objects/sms.php';
	$database = new Database();
	$db = $database->getConnection();
	$sms = new Sms($db);

	$stmt = $sms->getExpiredbefore3dayClientsPhone();
	$data = $stmt->rowCount();

	global $sms_expiring_3day_result;

	if ($data > 0) {

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			$num[] = $row['phone'];
			$id[] = $row['id'];
		}
		$ids =  implode(', ', $id);
	
        // Loop through the array and add the country code
        foreach ($num as &$number) {
            $number = "88". $number;
        }
        $numbers =  implode(',', $num);
        //$number =  "8801400559161,8801835559161";
        //echo $number;
//         echo $numbers;
// 		exit;

		$message = "WiFi ৩দিন পর অফ হবে,চালু রাখতে লিংকে বিল পে করুন\nbaycombd.com/paybill/";

		//Set the value
		$sms->ids = $ids;
		$sms_send_response = json_decode(bulkbd_sms_send($numbers, $message), true);
		
		//Bulk sms bd service response
		if ($sms_send_response['response_code'] == 202) {
			if ($sms->expiredClientSmsUpdate()) {
				$sms_expiring_3day_result = "200, Expiring before 3 day Client SMS sent successfully";
			}
		} else {
			$sms_expiring_3day_result = "[ 201, " . $sms_send_response['response_code'] . "]" .
				", " . $sms_send_response['error_message'];
		}
        
        
// 		//All sms gateway service response
// 		if ($sms_send_response['status'] === 'success') {
// // 			if ($sms->expiredClientSmsUpdate()) {
// // 				$sms_expiring_3day_result = "200, Expiring before 3 day Client SMS sent successfully";
// // 			}
// 			if (1) {
// 				$sms_expiring_3day_result = "200, Expiring before 3 day Client SMS sent successfully";
// 			}
// 		} else {
// 			$sms_expiring_3day_result = $sms_send_response['status'] . ", " . $sms_send_response['message'];
// 		}
		
	} else {
		$sms_expiring_3day_result = "404, No expiring client before 3 day";
	}
}

function expired_client_sms_send_disconnect()
{
	include_once '../config/database.php';
	include_once  '../objects/sms.php';
	include_once  '../objects/device.php';
	$database = new Database();
	$db = $database->getConnection();
	$sms = new Sms($db);

	$stmtppp = $sms->expiredClientsPPPname();
	$stmtphone = $sms->expiredClientsPhone();

	global $sms_expired_client_disconnect_result;

	if ($stmtppp->rowCount() > 0 && $stmtphone->rowCount() > 0) {

		//Collecting phone numbers and ppp name
		while ($row = $stmtppp->fetch(PDO::FETCH_ASSOC)) {

			$pppName[] = $row['ppp_name'];
			$id[] = $row['id'];
		}
		while ($row = $stmtphone->fetch(PDO::FETCH_ASSOC)) {

			$num[] = $row['phone'];
		}
		$ids =  implode(', ', $id);
		$numbers =  implode(', ', $num);

		$device = new Device($db);
		$stmt = $device->get_device_url();

		//retrieve the table contents
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			$url = $row['api_base'] . "pppListDisable.php";
			$login_ip = $row['login_ip'];
			$username = $row['username'];
			$password = $row['password'];
		}

		$postdata = array(
			'login_ip' => $login_ip,
			'username' => $username,
			'password' => $password,
			'ppp_names' => $pppName
		);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result = curl_exec($ch);
		curl_close($ch);

		//Disable and Remove form mikrotik server
		$mikrotik_response = json_decode($result, true);

		if ($mikrotik_response['status'] == 200) {

            $expired_client_message = "WiFi মেয়াদ শেষ, অটো চালু করতে লিংকে বিল পে করুন৷\nbaycombd.com/paybill/";
            
            
			//Set the value
			$sms->ids = $ids;
			$sms_send_response = json_decode(sms_send($numbers, $expired_client_message), true);

			if ($sms_send_response['response_code'] == 202) {
				if ($sms->clientDisconnectModeUpdate()) {
					$sms_expired_client_disconnect_result = "202, Expired client disconnect and sms send succcessfully";
				}
			} else {

				$sms_expired_client_disconnect_result = "[201, " . $sms_send_response['response_code'] . "]" .
					", " . $sms_send_response['error_message'];
			}
		} else {

			$sms_expired_client_disconnect_result = $mikrotik_response['status'] . ", " . $mikrotik_response['message'];
		}
	} else if ($stmtppp->rowCount() > 0) {

		while ($row = $stmtppp->fetch(PDO::FETCH_ASSOC)) {

			$pppName[] = $row['ppp_name'];
			$id[] = $row['id'];
		}
		$ids =  implode(', ', $id);

		$device = new Device($db);
		$stmt = $device->get_device_url();


		//retrieve the table contents
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			$url = $row['api_base'] . "pppListDisable.php";
			$login_ip = $row['login_ip'];
			$username = $row['username'];
			$password = $row['password'];
		}

		$postdata = array(
			'login_ip' => $login_ip,
			'username' => $username,
			'password' => $password,
			'ppp_names' => $pppName
		);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result = curl_exec($ch);
		curl_close($ch);

		$mikrotik_response = json_decode($result, true);

		if ($mikrotik_response['status'] == 200) {

			$sms->ids = $ids;

			if ($sms->clientDisconnectModeUpdate()) {
				$sms_expired_client_disconnect_result = "202, PPP disconnected successfully";
			}
		} else {

			$sms_expired_client_disconnect_result = $mikrotik_response['status'] . ", " . $mikrotik_response['message'];
		}
	} else {

		$sms_expired_client_disconnect_result = "404, Not found expired clients in this time.";
	}
}


function expired_take_time_client_sms_send_disconnect()
{
	include_once '../config/database.php';
	include_once  '../objects/sms.php';
	include_once  '../objects/device.php';
	$database = new Database();
	$db = $database->getConnection();
	$sms = new Sms($db);

	$stmtppp = $sms->getExpiredTakeTimeClientsPPPname();
	$stmtphone = $sms->getExpiredTakeTimeClientsPhone();

	global $expired_take_time_client_disconnect_result;

	if ($stmtppp->rowCount() > 0 && $stmtphone->rowCount() > 0) {

		//Collecting phone numbers and ppp name
		while ($row = $stmtppp->fetch(PDO::FETCH_ASSOC)) {

			$pppName[] = $row['ppp_name'];
			$id[] = $row['id'];
		}
		while ($row = $stmtphone->fetch(PDO::FETCH_ASSOC)) {

			$num[] = $row['phone'];
		}
		$ids =  implode(', ', $id);
		$numbers =  implode(', ', $num);

		$device = new Device($db);
		$stmt = $device->get_device_url();

		//retrieve the table contents
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			$url = $row['api_base'] . "pppListDisable.php";
			$login_ip = $row['login_ip'];
			$username = $row['username'];
			$password = $row['password'];
		}

		$postdata = array(
			'login_ip' => $login_ip,
			'username' => $username,
			'password' => $password,
			'ppp_names' => $pppName
		);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result = curl_exec($ch);
		curl_close($ch);

		//Disable and Remove form mikrotik server
		$mikrotik_response = json_decode($result, true);

		if ($mikrotik_response['status'] == 200) {

            $expired_client_message = "WiFi মেয়াদ শেষ, অটো চালু করতে লিংকে বিল পে করুন৷\nbaycombd.com/paybill/";
            
            
            
			//Set the value
			$sms->ids = $ids;
			$sms_send_response = json_decode(sms_send($numbers, $expired_client_message), true);

			if ($sms_send_response['response_code'] == 202) {
				if ($sms->clientDisconnectModeUpdate()) {
					$expired_take_time_client_disconnect_result = "202, Expired take time client disconnect and sms send succcessfully";
				}
			} else {

				$expired_take_time_client_disconnect_result = "[201, " . $sms_send_response['response_code'] . "]" .
					", " . $sms_send_response['error_message'];
			}
		} else {

			$expired_take_time_client_disconnect_result = $mikrotik_response['status'] . ", " . $mikrotik_response['message'];
		}
	} else if ($stmtppp->rowCount() > 0) {
		while ($row = $stmtppp->fetch(PDO::FETCH_ASSOC)) {

			$pppName[] = $row['ppp_name'];
			$id[] = $row['id'];
		}
		$ids =  implode(', ', $id);

		$device = new Device($db);
		$stmt = $device->get_device_url();


		//retrieve the table contents
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			$url = $row['api_base'] . "pppListDisable.php";
			$login_ip = $row['login_ip'];
			$username = $row['username'];
			$password = $row['password'];
		}

		$postdata = array(
			'login_ip' => $login_ip,
			'username' => $username,
			'password' => $password,
			'ppp_names' => $pppName
		);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result = curl_exec($ch);
		curl_close($ch);

		$mikrotik_response = json_decode($result, true);

		if ($mikrotik_response['status'] == 200) {

			$sms->ids = $ids;

			if ($sms->clientDisconnectModeUpdate()) {
				$expired_take_time_client_disconnect_result = "202, Take time PPP disconnected successfully";
			}
		} else {

			$expired_take_time_client_disconnect_result = $mikrotik_response['status'] . ", " . $mikrotik_response['message'];
		}
	} else {

		$expired_take_time_client_disconnect_result = "404, Not found take time expired clients in this time.";
	}
}

function sms_send($numbers, $message)
{
	include '../config/url_config.php';

	$data = [
		"api_key" => $sms_api_key,
		"senderid" => $sms_api_senderid,
		"number" => $numbers,
		"message" => $message
	];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $sms_api_url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
}

if ("09:00" <= date("H:i") && "18:00" >= date("H:i")) {
	expipering_3day_client_sms_send();
	expired_client_sms_send_disconnect();
	expired_take_time_client_sms_send_disconnect();
}

$dashboard = new Dashboard($db);
$employee = new Employee($db);
$logs_stmt = $employee->fetch_logs();
$dashboard->zone = $zone;

$expiredDataPoints = array(
	array("label" => "Expired", "y" => $dashboard->count_total_expired_client()),
	array("label" => "Mobile", "y" => $dashboard->expired_mobile()),
	array("label" => "Cash", "y" => $dashboard->expired_cash()),
);

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
		}
	</script>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>

	<div class="card text-center">

		<div class="card-body">

			<div class="alert alert-warning" role="alert">
				<h4 class="alert-heading">Notice</h4>
				<p># সর্বদা কাস্টমারদেরকে লিংক দিয়ে বিলে দিতে অবহিত করুন এবং বিলের লিংক SMS করে দিন।</p>
			</div>

			<div class="alert alert-success alert-dismissible fade show" role="alert">
				<?php echo $sms_expiring_3day_result; ?>
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				<?php echo $sms_expired_client_disconnect_result; ?>
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="alert alert-success alert-dismissible fade show" role="alert">
				<?php echo $expired_take_time_client_disconnect_result; ?>
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

		</div>

	</div>


	<div id="expiredchartContainer" style="height: 370px; width: 100%;"></div> <br>

	<div style="overflow-x: auto;">
		<?php if ($zone == 'All') { ?>
			<strong>Recent login details</strong>
			<table>
				<tr>
					<th>Time</th>
					<th>Details</th>
				</tr>

				<?php foreach ($logs_stmt as $logs) { ?>
					<tr>
						<td><?php echo $logs['time'] ?></td>
						<td><?php echo $logs['details'] ?></td>
					</tr>
			<?php }
			} ?>
			</table>
	</div>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>

</html>