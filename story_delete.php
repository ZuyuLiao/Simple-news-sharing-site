<?php
// Delete stories
require 'database.php';
session_start();
$username = $_SESSION['username'];
$id = $_POST['id'];
// CSRF token validate
if(!hash_equals($_SESSION['token'], $_POST['token'])){
    die("Request forgery detected");
}
else {
    // Token validated and then delete the comment first
    $stmt = $mysqli->prepare("delete from comments where id =?");
    if(!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->bind_param('i', $id);

    $stmt->execute();

    $stmt->close();

    // Then delete associated links
    $stmt = $mysqli->prepare("delete from links where id =?");
    if(!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->bind_param('i', $id);

    $stmt->execute();

    $stmt->close();

    // Then delete this story where "my favorites" contains
    $stmt = $mysqli->prepare("delete from favorites where id =?");
    if(!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->bind_param('i', $id);

    $stmt->execute();

    $stmt->close();

    // Last, delete the story from stories database
    $stmt = $mysqli->prepare("delete from stories where id =?");
    if(!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->bind_param('i', $id);

    $stmt->execute();

    $stmt->close();

    echo "<script>alert('Delete Story Success! Redirect to main page in 2 seconds')</script>";

    header("Refresh: 2; url = main.php");
}
?>