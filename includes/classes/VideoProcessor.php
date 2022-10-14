<?php
class VideoProcessor
{
    private $con;

    private $sizeLimit = 5100000; // 5.1 MB
    private $allowedTypes = ["mp4", "flv", "webm", "mkv", "vob", "ogv", "ogg", "avi", "wmv", "mov", "mpeg", "mpg"];
    private $ffmpegPath = "ffmpeg/ffmpeg";
    private $ffprobePath = "ffmpeg/ffprobe";

    public function __construct($dbCon)
    {
        $this->con = $dbCon;
    }

    public function upload($videoUploadData)
    {
        $targetDir = "uploads/videos/";
        $videoData = $videoUploadData->videoDataArray;
        $tempFilePath = $targetDir . uniqid() . basename($videoData["name"]);
        $tempFilePath = str_replace(" ", "_", $tempFilePath);
        $tempFilePath = str_replace(array('!', '"', '#', '$', '&', "'", '(', ')', '*', ',', ';', '<', '>', '?', '[', '\\', ']', '^', '`', '{', '|', '}'), '', $tempFilePath);

        $isValidData = $this->processData($videoData, $tempFilePath);
        if (!$isValidData) {
            return false;
        }

        // Deployment tmp folder and write permission for videos folder
        // maybe set root path for index.php     define ('SITE_ROOT', realpath(dirname(__FILE__)));
        if (move_uploaded_file($videoData["tmp_name"], $tempFilePath)) {
            $finalFilePath = $targetDir . uniqid() . ".mp4";
            if (!$this->insertVideoData($videoUploadData, $finalFilePath)) {
                echo "Insert query failed";
                return false;
            }
            if (!$this->convertVideoToMp4($tempFilePath, $finalFilePath)) {
                echo "Video conversion failed.";
                return false;
            }

            if (!$this->deleteFile($tempFilePath)) {
                echo "Video deletion failed.";
                return false;
            }

            if (!$this->generateThumbnails($finalFilePath)) {
                echo "Thumbnail generation failed.";
                return false;
            }
            return true;
        }
    }

    // Improvements - don't convert already mp4, rollback, narration, properly escape

    private function processData($videoData, $filePath)
    {
        $videoType = pathinfo($filePath, PATHINFO_EXTENSION);

        if (!$this->isValidSize($videoData)) {
            echo "File size too large. Maximum size can be " . $this->sizeLimit . " bytes.";
            return false;
        } else if (!$this->isValidType($videoType)) {
            echo "Invalid file type";
            return false;
        } else if ($this->hasErrors($videoData)) {
            echo "Error code : " . $videoData["error"];
            return false;
        }
        return true;
    }

    private function isValidSize($data)
    {
        // Deployment - php_value upload_max_filesize 500M
        // php_value memory_limit 500M
        // php_value post_max_size 500M
        // php_value max_input_time 60
        // php_value max_execution_time 60
        // test large files in production
        return $data["size"] <= $this->sizeLimit;
    }

    private function isValidType($type)
    {
        $lowerCased = strtolower($type);
        return in_array($lowerCased, $this->allowedTypes);
    }

    private function hasErrors($videoData)
    {
        return $videoData["error"] != 0;
    }

    private function insertVideoData($videoUploadData, $finalFilePath)
    {
        $query = $this->con->prepare("INSERT INTO videos (title, uploadedBy, description, privacy, category, filePath)
                                      VALUES (:title, :uploadedBy, :description, :privacy, :category, :filePath)");

        $query->bindParam(":title", $videoUploadData->title);
        $query->bindParam(":uploadedBy", $videoUploadData->uploadedBy);
        $query->bindParam(":description", $videoUploadData->description);
        $query->bindParam(":privacy", $videoUploadData->privacy);
        $query->bindParam(":category", $videoUploadData->category);
        $query->bindParam(":filePath", $finalFilePath);

        return $query->execute();
    }

    private function convertVideoToMp4($tempFilePath, $finalFilePath)
    {
        // Deployment - two 80 mb exe files on server
        $cmd = "$this->ffmpegPath -i $tempFilePath $finalFilePath 2>&1"; // 2>&1 to show errors
        $outputLog = [];
        exec($cmd, $outputLog, $returnCode);

        if ($returnCode != 0) { // failed
            foreach ($outputLog as $log) {
                echo $log . "<br>";
            }
            return false;
        }
        return true;
    }

    private function deleteFile($tempFilePath)
    {
        if (!unlink($tempFilePath)) {
            echo "Could not delete file";
            return false;
        }
        return true;
    }

    public function generateThumbnails($filePath)
    {
        $thumbnailSize = "210x118";
        $numThumbnails = 3;
        $pathToThumbnail = "uploads/videos/thumbnails";

        $duration = $this->getVideoDuration($filePath);
        $videoId = $this->con->lastInsertId();
        $this->updateDuration($duration, $videoId);

        for ($num = 1; $num <= $numThumbnails; $num++) {
            $imageName = uniqid() . ".jpg";
            $interval = ($duration * 0.8) / $numThumbnails * $num;
            $fullThumbnailPath = "$pathToThumbnail/$videoId-$imageName";

            $cmd = "$this->ffmpegPath -i $filePath -ss $interval -s $thumbnailSize -vframes 1 $fullThumbnailPath 2>&1"; // 2>&1 to show errors
            $outputLog = [];
            exec($cmd, $outputLog, $returnCode);

            if ($returnCode != 0) { // failed
                foreach ($outputLog as $log) {
                    echo $log . "<br>";
                }
            }
            $query = $this->con->prepare("INSERT INTO thumbnails(videoId, filePath , selected) VALUES (:videoId, :filePath, :selected)");
            $query->bindParam(":videoId", $videoId);
            $query->bindParam(":filePath", $fullThumbnailPath);
            $query->bindParam(":selected", $selected);

            $selected = $num == 1 ? 1 : 0;
            $success = $query->execute();
            if (!$success) {
                echo "Error inserting thumbnail";
                return false;
            }
        }
        return true;
    }

    private function getVideoDuration($filePath)
    {
        return (int) shell_exec("$this->ffprobePath -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $filePath");
    }

    private function updateDuration($duration, $videoId)
    {
        $hours = floor($duration / 3600);
        $mins = floor(($duration - ($hours * 3600)) / 60);
        $secs = floor($duration % 60);

        $hours = ($hours < 1) ? "" : $hours . ":";
        $mins = ($mins < 10) ? "0" . $mins . ":" : $mins . ":";
        $secs = ($secs < 10) ? "0" . $secs : $secs;
        $duration = $hours . $mins . $secs;

        $query = $this->con->prepare("UPDATE videos SET duration=:duration WHERE id=:videoId");
        $query->bindParam(":duration", $duration);
        $query->bindParam(":videoId", $videoId);
        $query->execute();
    }
}
