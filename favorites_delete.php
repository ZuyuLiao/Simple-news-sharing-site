<?php

require 'database.php';
session_start();
$username = $_SESSION['username'];
$id = $_POST['id'];
// CSRF token validate
if(!hash_equals($_SESSION['token'], $_POST['token'])){
    die("Request forgery detected");
}
else {
    // Token validated and then delete
    $stmt = $mysqli->prepare("delete from favorites where id =?");
    if(!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->bind_param('i', $id);

    $stmt->execute();

    $stmt->close();

    echo "<script>alert('Delete Success!')</script>";
    header("Location: favorites.php");
}
?>