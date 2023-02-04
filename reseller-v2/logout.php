<?php
session_start();
unset($_SESSION['loged']);
unset($_SESSION['admin_id']);
header('location: index.php');
die();
exit();
