<?php require_once "includes/config.php";?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YT Clone</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js" integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous"></script>
    <script src="assets/js/commonActions.js"></script>
</head>
<body>
    <div id="pageContainer">
        <div id="mastHeadContainer">
            <button class="navShowHide">
                <img src="assets/images/icons/menu.png" alt="menu">
            </button>
            <a href="index.php" class="logoContainer">
                <img src="assets/images/icons/playTubeLogo.jpg" title="logo" alt="siteLogo">
            </a>
            <div class="searchBarContainer">
                <form action="search.php" method="GET">
                    <input type="text" class="searchBar" name="term" placeholder="Search...">
                    <button class="searchButton">
                        <img src="assets/images/icons/search.png" alt="search">
                    </button>
                </form>
            </div>
            <div class="rightIcons">
                <a href="upload.php">
                    <img class="upload" src="assets/images/icons/upload.png" alt="upload">
                </a>
                <a href="#">
                    <img class="profilePicture" src="assets/images/profilePictures/default.png" alt="profilePicture">
                </a>
            </div>
        </div>
        <div id="sideNavContainer" style="display:none"></div>
        <div id="mainSectionContainer">
            <div id="mainContentContainer">
