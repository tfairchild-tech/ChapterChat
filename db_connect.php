<?php
// Create connection
$conn = new mysqli("chapterchat.us", "cceduuser", "Edu4Read$");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?> 