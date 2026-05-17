<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "customer_management";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("<div style='color:red;font-family:Arial;padding:30px;text-align:center'>
        <h3>❌ Database Connection Failed!</h3>
        <p>" . mysqli_connect_error() . "</p>
        <p>Please make sure MySQL is running and run <b>database.sql</b> in phpMyAdmin.</p>
    </div>");
}

// ─── AUTH CHECK FUNCTION ───
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
?>
