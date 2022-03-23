<?php
$error = $mobile_no = "";

if (isset($_POST['next'])) {
    $mobile_no = $_POST['mobile_no'];
    if (empty($mobile_no)) {
        $error = '<div class="alert alert-danger">সঠিক মোবাইল নাম্বারটি দিন</div>';
    } else if (strlen($mobile_no) != 11) {
        $error = '<div class="alert alert-danger">মোবাইল নাম্বারটি ১১ ডিজিটের হতে হবে</div>';
    } else {

        header('location: info.php?mobile_no=' . $mobile_no);
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

<body >
    <?php include('header.html') ?>
    <div class="main_content">
        <div class="container">
            <div  class="card">
                <h6 class="card-title text-center">
                    রেজিস্ট্রেশনকৃত মোবাইল নাম্বারটি দিন
                </h6>
                <span><?php echo $error; ?></span>
                <form action="index.php" method="post">
                    <input type="tel" maxlength="11" name="mobile_no" value="<?php echo $mobile_no ?>" class="form-control input-btn mt-2"><br>
                    <div class="input-btn">
                        <button type="submit" name="next" class="form-control btn btn-secondary">Next</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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