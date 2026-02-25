<?php
$conn = new mysqli("localhost", "root", "amo", "school_portal_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>