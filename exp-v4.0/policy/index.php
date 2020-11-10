<?php

header("Content-Type: text/html;charset=utf-8");

$serverName = "localhost";
$userName = "creative_exp_int";
$password = "saifulcse0001";
$dbname = "creative_expert_internet";


$conn = mysqli_connect($serverName,$userName,$password,$dbname);

mysqli_query($conn, 'SET CHARACTER SET utf8');
mysqli_query($conn, "SET SESSION collation_connection = 'utf8_general_ci'");

$query = "SELECT * FROM policy";

$result = mysqli_query($conn, $query);

?>


<!DOCTYPE html>
<html>
<title>Privacy and policy</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<style>
body,h1,h2,h3,h4,h5 {font-family: "Raleway", sans-serif}
.ali{
    margin: auto;
}
</style>
<body class="w3-light-grey">

<!-- w3-content defines a container for fixed size centered content, 
and is wrapped around the whole page content, except for the footer in this example -->
<div class="w3-content" style="max-width:1400px">

<!-- Header -->
<header class="w3-container w3-center w3-padding-32"> 
  <h1><b>Privacy and policy</b></h1>
  <p>Expert Internet</p>
</header>

<!-- text entries -->
<div>
  <!-- Blog entry -->
  <div class="w3-card-4 w3-margin w3-white">
    <div class="w3-container">
        <?php 
            while($row = mysqli_fetch_array($result)){  ?>
                <div class="w3-container">
                    <h3><b><?php echo $row['title']; ?></b></h3>
                </div>

                <div class="w3-container">
                    <p><?php echo $row['description']; ?></p>
                </div>

            <?php } ?>
    
    </div>

  </div>

<!-- end text entries -->
</div><br>

<!-- END w3-content -->
</div>

<!-- Footer -->
<footer class="w3-container w3-dark-grey w3-padding-32 w3-margin-top" >
  <p style="text-align:center">&copy; 2019 Expert Internet Service. All rights reserved. Cox's Bazar | Bangladesh</p>
</footer>

</body>
</html>



