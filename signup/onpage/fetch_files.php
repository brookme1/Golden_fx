<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "file_upload_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$files_result = $conn->query("SELECT filename, file_path FROM files");

while ($file = $files_result->fetch_assoc()) {
    echo '<li><a href="' . $file['file_path'] . '" target="_blank">' . $file['filename'] . '</a></li>';
}

$conn->close();
?>
