<?php
session_start();

$error = $admin_id = $admin_pin = "";

if (isset($_POST['login'])) {
    $admin_id = $_POST['admin_id'];
    $admin_pin = $_POST['admin_pin'];
    if (empty($admin_id)) {
        $error = '<div class="alert alert-danger">Enter Correct Admin ID</div>';
    } else if (strlen($admin_id) != 4) {
        $error = '<div class="alert alert-danger">Enter Correct Admin ID</div>';
    } else if (empty($admin_pin)) {
        $error = '<div class="alert alert-danger">Enter Correct Pin</div>';
    } else if (strlen($admin_pin) != 4) {
        $error = '<div class="alert alert-danger">Enter Correct Pin</div>';
    } else if($admin_id == 6606 && $admin_pin == 4563){

        $_SESSION['loged'] = 'loged';
        header('location: client_list.php');
        
    }else{
        $error = '<div class="alert alert-danger">Wrong ID or Pin</div>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Expert Internet Ltd.
    </title>

    <!-- bootstrap linked-->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body>
    <?php include('header.html') ?>
    <div class="main_content">
        <div class="container">
            <div class="card">
                <h6 class="card-title text-center">
                    Login
                </h6>
                <span><?php echo $error; ?></span>
                <form action="index.php" method="post">
                    <label>Admin ID</label>
                    <input type="number" maxlength="4" name="admin_id" value="<?php echo $admin_id ?>" class="form-control input-btn mt-2"><br>
                    <label>Pin</label>
                    <input type="pin" maxlength="4" name="admin_pin" value="<?php echo $admin_pin ?>" class="form-control input-btn mt-2"><br>
                    <div class="input-btn">
                        <button type="submit" name="login" class="form-control btn btn-secondary">Login</button>
                    </div>
                </form>
            </div>

        </div>

    </div>
    <p style="text-align: center; color: gray">An Internet Service Provider (ISP) of Bay Communication, Cox's Bazar</p>
    <?php include('footer.html') ?>
    <!--card end here-->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
</body>

</html>