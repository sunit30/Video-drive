<?php require_once "includes/header.php";
require_once "includes/classes/VideoPlayer.php";
require_once "includes/classes/VideoInfoSection.php";

if (!isset($_SESSION["userLoggedIn"])) {
    echo "User not logged in";
    exit();
}

$query = $con->prepare("SELECT * from videos WHERE uploadedBy=:username");
$uName = $userLoggedInObj->getUsername();
$query->bindParam(":username", $uName);
$query->execute();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $vidId = $row["id"];
}
if (empty($vidId)) {
    echo "Please upload a video ( format: mp4,  max: 5mb )";
    ?>
    <a href="upload.php">
                    <img class="upload" src="assets/images/icons/upload.png" title="upload" alt="upload">
                </a>
<?php
exit();
}

$video = new Video($con, $vidId, $userLoggedInObj);
$video->incrementViews();
?>
<script src="assets/js/videoPlayerActions.js"></script>

<!-- <div class="suggestions"> </div> -->
<div class="watchLeftColumn">
<?php
$videoPlayer = new VideoPlayer($video);
echo $videoPlayer->create(true);

$videoPlayer = new VideoInfoSection($con, $video, $userLoggedInObj);
echo $videoPlayer->create();
?>
</div>

<!-- <div class="suggestions">

</div> -->

<?php require_once "includes/footer.php";?>
