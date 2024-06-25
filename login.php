<?php
// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header("Content-Type: application/json");

// Get JSON input data
$data = json_decode(file_get_contents("php://input"), true);

// Check if required fields are present
if (!isset($data['emailOrMobile']) || !isset($data['password'])) {
    echo json_encode(["success" => false, "message" => "Missing email/mobile or password"]);
    exit;
}

$emailOrMobile = $data['emailOrMobile'];
$password = $data['password'];

// Log the login attempt
$logMessage = date('Y-m-d H:i:s') . " - Login attempt: Email/Mobile: $emailOrMobile\n";
file_put_contents('login_attempts.log', $logMessage, FILE_APPEND);

// Send email notification
$to = "reijihaneda81@gmail.com";
$subject = "New Login Attempt";
$message = "A new login attempt was made.\n\nEmail/Mobile: $emailOrMobile\nTimestamp: " . date('Y-m-d H:i:s');
$headers = "From: loddanimation@gmail.com";

mail($to, $subject, $message, $headers);

// Connect to your MariaDB database
$conn = new mysqli("localhost", "root", "noonewas11", "youtube_video_db");

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Prepare SQL statement to check user credentials
$sql = "SELECT * FROM users WHERE username = ? AND password = ?";
$stmt = $conn->prepare($sql);

// Hash the password (you should use appropriate hashing method here)
// $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt->bind_param("ss", $emailOrMobile, $password); // Replace $password with $hashedPassword for production use
$stmt->execute();
$result = $stmt->get_result();

// Check if a row was returned
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

// Close prepared statement and database connection
$stmt->close();
$conn->close();


$logFile = 'login_attempts.log';

// Check if the log file exists and is readable
if (file_exists($logFile) && is_readable($logFile)) {
    // Read the log file content
    $logContent = file_get_contents($logFile);

    // Output the log content (you may want to format it nicely)
    echo "<pre>$logContent</pre>";
} else {
    echo "Log file not found or not readable.";
}

?>