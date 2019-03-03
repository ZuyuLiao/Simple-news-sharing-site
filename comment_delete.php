<?php
// Delete comments
require 'database.php';
session_start();
$username = $_SESSION['username'];
$c_id = $_POST['c_id'];
// CSRF token validate
if(!hash_equals($_SESSION['token'], $_POST['token'])){
    die("Request forgery detected");
}
else {
    // Token validated and then delete the comment
    $stmt = $mysqli->prepare("delete from comments where c_id =?");
    if(!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->bind_param('i', $c_id);

    $stmt->execute();

    $stmt->close();

    echo "<script>alert('Delete comment Success!')</script>";
    echo "<script>window.close()</script>";
}
?>