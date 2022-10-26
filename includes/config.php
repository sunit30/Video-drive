<?php
ob_start(); // waits until all code is loaded
session_start();

date_default_timezone_set("Europe/London");

try {
    // change this for deployment and pword through env in bash.rc file
    $con = new PDO("mysql:dbname=youtube_clone;host=localhost", "root", getenv('MYSQL_YTC_P'));
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
