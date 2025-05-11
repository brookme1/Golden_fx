<?php
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_auth";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Get JSON input
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Debugging: Log received JSON
file_put_contents("log.txt", print_r($data, true)); // Check this file for debugging

if (json_last_error() !== JSON_ERROR_NONE) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid JSON: ' . json_last_error_msg()]));
}

// Check if data is received
if (!$data) {
    die(json_encode(['status' => 'error', 'message' => 'No data received']));
}

// Sanitize inputs
$fullName = filter_var($data['fullName'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $data['password'] ?? '';
$confirmPassword = $data['confirmPassword'] ?? '';

// Validate inputs
$errors = [];
if (empty($fullName)) $errors[] = 'Full name is required';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
if (empty($password) || strlen($password) < 8) $errors[] = 'Password must be at least 8 characters';
if ($password !== $confirmPassword) $errors[] = 'Passwords do not match';

if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'message' => implode(', ', $errors)]);
    exit;
}

// Check if email exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
    exit;
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $fullName, $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Registration successful!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Registration failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
