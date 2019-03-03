<?php
require 'database.php';
session_start();
if(!isset($_SESSION['username'])) {
    echo "You are not logged in. Redirect to news page in 2 seconds.";
    header("Refresh: 2; url = main0.php");
}
else {
    $username = $_SESSION['username'];
    if(isset($_POST['like'])) {
        $id = $_POST['id'];
        // CSRF token validate
        if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }

        // Check duplicates
        $stmt = $mysqli->prepare("select id from favorites where username=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt->bind_param('s', $username);

        $stmt->execute();

        $stmt->bind_result($ids);

        while($stmt->fetch()){
            if($id == $ids) {
                // Contains duplicate
                $flag = true;
                break;
            }
            else
                $flag = false;
        }
        $stmt->close();

        if($flag) {
            echo "<script>alert('You already liked it!')</script>";
            echo "<script>window.close()</script>";
        }
        else {
            // Insert this story into favorites database
            $stmt = $mysqli->prepare("insert into favorites (id, username) values (?,?)");
            if(!$stmt) {
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }

            $stmt->bind_param('is', $id, $username);

            $stmt->execute();

            $stmt->close();

            echo "<script>alert('Like it Success!')</script>";
            echo "<script>window.close()</script>";
        }
    }

}
?>