<?php
ob_start(); //This turn on output buffering
session_start();


$timezone = date_default_timezone_set("Asia/Jerusalem");
$con = mysqli_connect("localhost", "root", "", "social");

if (mysqli_connect_errno()) {
    echo "Failed to connect: " . mysqli_connect_errno();
}
