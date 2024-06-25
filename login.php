<?php
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$emailOrMobile = $data['emailOrMobile'];
$password = $data['password'];

// Log the login attempt
$logMessage = date('Y-m-d H:i:s') . " - Login attempt: Email/Mobile: $emailOrMobile\n";
file_put_contents('login_attempts.log', $logMessage, FILE_APPEND);

// Send email notification
$to = "reijihaneda81@gmail.com";
$subject = "New Login Attempt";
$message = "A new login attempt was made.\n\nEmail/Mobile: $emailOrMobile\nTimestamp: " . date('Y-m-d H:i:s');
$headers = "From: yoursystem@example.com";

mail($to, $subject, $message, $headers);

// Connect to your MariaDB database
$conn = new mysqli("localhost", "root", "noonewas11", "youtube_video_db");

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Check user credentials (use prepared statements and password hashing in production)
$sql = "SELECT * FROM users WHERE username = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $emailOrMobile, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => true, "message" => "Login successful"]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
}

// Store the login attempt in the database
$sql = "INSERT INTO login_attempts (email_or_mobile, timestamp) VALUES (?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $emailOrMobile);
$stmt->execute();

$stmt->close();
$conn->close();

USE youtube_video_db;
CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email_or_mobile VARCHAR(100) NOT NULL,
    timestamp DATETIME NOT NULL
);
?>

