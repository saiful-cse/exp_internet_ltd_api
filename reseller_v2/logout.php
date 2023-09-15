<?php
session_start();
unset($_SESSION['loged']);
unset($_SESSION['employee_id']);
header('location: index.php');
die();
exit();
