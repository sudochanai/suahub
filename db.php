<?php
$host = "127.0.0.1";
$port = "5432";
$dbname = "suahub";
$user = "subzero";
$password = "247game365";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}



