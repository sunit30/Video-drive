<?php require_once "includes/header.php";?>

hello

<?php

//test
$query = $con->prepare("SELECT * from categories");
$query->execute();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    echo $row["name"] . "<br>";
}
//

require_once "includes/footer.php";?>
