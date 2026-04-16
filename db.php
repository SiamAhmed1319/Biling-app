<?php
$hostname = "127.0.0.1";
$username = "root";
$password = "root"; // KSWEB বা ফোনে সাধারণত পাসওয়ার্ড খালি থাকে
$dbname = "billing_db";

$conn = mysqli_connect($hostname, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>