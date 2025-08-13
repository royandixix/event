<?php

$host     = "localhost";
$user     = "root";
$password = "";
$dbname   = "penaftaran"; 

$db = mysqli_connect($host, $user, $password, $dbname);

if (!$db) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

