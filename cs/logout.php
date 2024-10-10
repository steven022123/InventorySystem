<?php
session_start();
session_destroy(); // Destroy the session
header("Location: lan.php"); // Redirect to login page
exit;
?>
