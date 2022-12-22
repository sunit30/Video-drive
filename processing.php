<?php
require_once "includes/header.php";
require_once "includes/classes/VideoUploadData.php";
require_once "includes/classes/VideoProcessor.php";

if (!isset($_POST["uploadButton"])) {
    echo "No file sent to page.";
    exit();
}

if (!str_ends_with($_FILES["fileInput"]["name"], 'mp4')) {
    echo "Please upload a file in mp4 format";
    sleep(5);
    header("Location: index.php");
    exit();
}

$videoUploadData = new VideoUploadData(
    $_FILES["fileInput"],
    $_POST["titleInput"],
    $_POST["descriptionInput"],
    $_POST["privacyInput"],
    $_POST["categoryInput"],
    $userLoggedInObj->getUsername(),
);

$videoProcessor = new VideoProcessor($con);
$wasSuccessful = $videoProcessor->upload($videoUploadData);

if ($wasSuccessful) {
    echo "Upload successful";
    header("Location: index.php");
}
