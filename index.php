<?php require_once "includes/header.php";?>

<?php
if (isset($_SESSION["userLoggedIn"])) {
    //echo "User logged in as " . $userLoggedInObj->getName();
    header('Location: watch.php');
} else {
    //echo "hello";
    header('Location: signIn.php');
}
?>

<?php require_once "includes/footer.php";?>
