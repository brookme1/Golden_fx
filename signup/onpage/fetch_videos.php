<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "file_upload_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$videos_result = $conn->query("SELECT video_name, video_path FROM videos");

while ($video = $videos_result->fetch_assoc()) {
    echo '<div class="col-md-6">
             <video controls width="100%">
                 <source src="' . $video['video_path'] . '" type="video/mp4">
                 Your browser does not support the video tag.
             </video>
          </div>';
}

$conn->close();
?>
