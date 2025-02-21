<?php
$conn = new mysqli("localhost","root","","social_media_web");

// Check connection
if ($conn -> connect_errno) {
  echo "Failed to connect to MySQL: " . $conn -> connect_error;
  exit();
}