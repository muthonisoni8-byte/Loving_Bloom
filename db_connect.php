<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "loving_blooms_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>