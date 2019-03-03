<?php
require 'database.php';
session_start();
if(!isset($_SESSION['username'])) {
    echo "You are not logged in. Redirect to news page in 2 seconds.";
    header("Refresh: 2; url = main0.php");
}
else {
    $username = $_SESSION['username'];
    if(isset($_POST['submit'])) {
        if(!isset($_POST['comment']) || empty($_POST['comment'])) {
            echo "Invalid comment.";
            exit;
        }
        else {
            $comment = $_POST['comment'];
            $id = $_POST['id'];
            // CSRF token validate
            if(!hash_equals($_SESSION['token'], $_POST['token'])){
                die("Request forgery detected");
            }
            // Inputs validated and then Insert them into comments database
            $stmt = $mysqli->prepare("insert into comments (id, username, content) values (?,?,?)");
            if(!$stmt) {
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }

            $stmt->bind_param('iss', $id, $username, $comment);

            $stmt->execute();

            $stmt->close();

            echo "<script>window.close()</script>";
        }
    }
}
?>