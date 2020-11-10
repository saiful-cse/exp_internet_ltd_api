<?php

$error = "";
$first_date = "";
$last_date = "";
$api = "";
$totalCreditDebitApi = "";

if (isset($_GET['view'])) {
    if (empty($_GET['first_date'])) {
        $error = "Select first date";
    } else if (empty($_GET['last_date'])) {
        $error = "Select last date";
    }else if(empty($_GET['password'])){
        $error = "Enter correct password";
    }else if($_GET['password'] != "saiful@#21490"){
        $error = "Enter correct password";
    }
     else {
        $first_date = $_GET['first_date'];
        $last_date = $_GET['last_date'];

        //echo "You have selected: ".$first_date."<br>".$last_date;
        $api = "http://creativesaif.com/api/exp-v3.1/txn/all_txn.php?" . "first_date=" . $first_date . "&last_date=" . $last_date;
        $totalCreditDebitApi = "http://creativesaif.com/api/exp-v3.1/txn/total_credit_debit.php?" . "first_date=" . $first_date . "&last_date=" . $last_date;
    }
}

function fetchTxn($api)
{
    //  Initiate curl
    $ch = curl_init();
    // Disable SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Set the url
    curl_setopt($ch, CURLOPT_URL, $api);
    // Execute
    $file = curl_exec($ch);
    // Closing
    curl_close($ch);

    $decoded_data = json_decode($file, true);
    return $decoded_data;
}

$getTxn = fetchTxn($api);
$getTotalCreditDebit = fetchTxn($totalCreditDebitApi);

?>

<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: arial, sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }

        h3 {
            text-align: center;
        }

        #company_header {
            text-align: center;
        }

        #total {
            text-align-last: right;
            margin-right: 30px;
        }

        #error {
            color: #FF0000;
        }
    </style>
</head>

<body>

    <div id="company_header">
        <strong>Expert Internet Ltd.</strong> <br>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <input type="date" name="first_date" value="<?php echo $_GET['first_date'] ?>"> <span>To</span>
            <input type="date" name="last_date" value="<?php echo $_GET['last_date'] ?>">
            <input type="password" name="password">
            <input type="submit" name="view" value="View">
        </form>
        <br>
        <span id="error"><?php echo $error ?></span>
    </div>

    <table>
        <tr>
            <th>Date</th>
            <th>TxnID</th>
            <th>Details</th>
            <th>Credit</th>
            <th>Debit</th>
        </tr>

        <?php if (is_array($getTxn['all_txn'])) {
            foreach ($getTxn['all_txn'] as $eachTxn) { ?>
                <tr>
                    <td><?php echo $eachTxn['date']; ?></td>
                    <td><?php echo $eachTxn['txn_id']; ?></td>
                    <td><?php echo $eachTxn['details']; ?></td>
                    <td><?php echo $eachTxn['credit']; ?></td>
                    <td><?php echo $eachTxn['debit']; ?></td>
                </tr>
            <?php } ?>
        <?php } ?>


    </table>

    <div id="total">
        <br>
        <strong><?php echo "Total Credit: ".$getTotalCreditDebit['total_credit']; ?></strong> <br>
        <strong><?php echo "Total Debit: ".$getTotalCreditDebit['total_debit']; ?></strong>
    </div>

</body>

</html>