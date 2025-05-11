<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "file_upload_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$pictures_result = $conn->query("SELECT picture_name, picture_path FROM pictures");

while ($picture = $pictures_result->fetch_assoc()) {
    echo '<div class="col-md-3">
             <img src="' . $picture['picture_path'] . '" class="img-fluid" alt="' . $picture['picture_name'] . '">
          </div>';
}

$conn->close();
?>
