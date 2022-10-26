<?php require_once "includes/header.php";?>

<?php
if (isset($_SESSION["userLoggedIn"])) {
    echo "User logged in as " . $_SESSION["userLoggedIn"];
} else {
    echo "hello";
}
?>

<?php

require_once "includes/footer.php";?>
