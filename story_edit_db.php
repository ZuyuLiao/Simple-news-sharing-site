<?php
// Edit stories
require 'database.php';
session_start();
$username = $_SESSION['username'];
if(isset($_POST['submit'])) {
    if(!isset($_POST['title']) || empty($_POST['title'])) {
        echo "Invalid Title. Back to the add news page in 2 seconds.";
        header("Refresh: 2; url = story_edit.php");
        exit;
    }
    else if(!isset($_POST['content']) || empty($_POST['content'])) {
        echo "Invalid Content. Back to the add news page in 2 seconds.";
        header("Refresh: 2; url = story_edit.php");
        exit;
    }
    else {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $id = $_POST['id'];
        // CSRF token validate
        if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }
        // Inputs validated and then update them into stories database
        $stmt = $mysqli->prepare("update stories set title=? , content=? where id =?");
        if(!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt->bind_param('ssi', $title, $content, $id);

        $stmt->execute();

        $stmt->close();

        echo "Edit success! Back to News Site in 2 seconds.";
        header("Refresh: 2; url = main.php");
    }
}
?>