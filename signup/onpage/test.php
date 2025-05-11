<?php
$filepath = "uploads/files/Use Case Diagrams.pdf"; // Change to actual file path

if (file_exists($filepath)) {
    echo "✅ File exists: " . $filepath;
} else {
    echo "❌ File not found: " . realpath($filepath);
}
?>
