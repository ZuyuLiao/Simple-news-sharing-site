<?php
// Delete user
require 'database.php';
session_start();
$username = $_SESSION['username'];
// CSRF token validate
if(!hash_equals($_SESSION['token'], $_POST['token'])){
    die("Request forgery detected");
}
else {
    // Token validated and then delete the comment by current user first
    $stmt = $mysqli->prepare("delete from comments where username =?");
    if(!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->bind_param('s', $username);

    $stmt->execute();

    $stmt->close();

    // Then Select story id whose author is current user
    $stmt = $mysqli->prepare("select id from stories where username=? ");
    if(!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->bind_param('s', $username);

    $stmt->execute();

    $stmt->bind_result($id);

    // Store story id into an array
    $a = array();
    while($stmt->fetch()) {
        array_push($a, $id);
    }
    $stmt->close();

    for($i = 0; $i < count($a); $i++) {
        $curr_id = $a[$i];
        // Then delete comments that belongs to the stories whose author is current user
        $stmt1 = $mysqli->prepare("delete from comments where id =?");
        if(!$stmt1) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt1->bind_param('i', $curr_id);

        $stmt1->execute();

        $stmt1->close();

        // Then delete associated links
        $stmt2 = $mysqli->prepare("delete from links where id =?");
        if(!$stmt2) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt2->bind_param('i', $curr_id);

        $stmt2->execute();

        $stmt2->close();

        // Then delete this story where "my favorites" contains
        $stmt3 = $mysqli->prepare("delete from favorites where id =?");
        if(!$stmt3) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt3->bind_param('i', $curr_id);

        $stmt3->execute();

        $stmt3->close();

        // Last, delete the story from stories database
        $stmt4 = $mysqli->prepare("delete from stories where id =?");
        if(!$stmt4) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt4->bind_param('i', $curr_id);

        $stmt4->execute();

        $stmt4->close();
    }

    // Delete user
    $stmt = $mysqli->prepare("delete from users where username =?");
    if(!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->bind_param('s', $username);

    $stmt->execute();

    $stmt->close();

    session_destroy();

    echo "Delete User Success! Redirect to main page in 2 seconds!";

    header("Refresh: 2; url = main0.php");
}
?>