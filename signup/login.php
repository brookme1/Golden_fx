<?php
header('Content-Type: application/json');

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_auth";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

$data = json_decode(file_get_contents('php://input'), true);

// Sanitize inputs
$email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $data['password'] ?? '';

// Validate inputs
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address']);
    exit;
}

if (empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Password is required']);
    exit;
}

// Check if user exists and fetch status
$stmt = $conn->prepare("SELECT id, full_name, password, status FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No account found with this email']);
    exit;
}

$user = $result->fetch_assoc();

// Check if user is blocked
if ($user['status'] == 1) { // 1 means blocked
    echo json_encode(['status' => 'error', 'message' => 'Your account has been blocked. Contact admin.']);
    exit;
}

// Verify password
if (!password_verify($password, $user['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Incorrect password. Please try again']);
    exit;
}

// Login successful
echo json_encode([
    'status' => 'success',
    'message' => 'Login successful!',
    'user' => [
        'id' => $user['id'],
        'name' => $user['full_name'],
        'email' => $email
    ]
]);

$stmt->close();
$conn->close();
?>
