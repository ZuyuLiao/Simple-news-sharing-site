<?php
require 'database.php';

if(!isset($_POST['username']) || !preg_match('/^[\w_\-]+$/', $_POST['username'])) {
    echo "Invalid Username. Back to the sign up page in 2 seconds.";
    header("Refresh: 2; url = login_signup.html");
    exit;
}
else if(!isset($_POST['password']) || !preg_match('/^[\w_\-]+$/', $_POST['password'])) {
    echo "Invalid Password. Back to the sign up page in 2 seconds.";
    header("Refresh: 2; url = login_signup.html");
    exit;
}
else {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $flag = true; // boolean type to check whether the username exists
    // select the users database to check whether the username is a duplicate
    $stmt = $mysqli->prepare("select username from users");
    if(!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->execute();

    $stmt->bind_result($names);

    while($stmt->fetch()){
        if($username == $names) {
            $flag = false;
            break;
        }
        else {
            $flag = true;
        }
    }

    $stmt->close();

    if($flag) {
        // valid username
        session_start();
        $_SESSION['username'] = $username;
        // create CSRF token
        $_SESSION['token'] = bin2hex(random_bytes(32));

        // insert new user info into database
        $pwd_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $mysqli->prepare("insert into users (username, password) values (?, ?)");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt->bind_param('ss', $username, $pwd_hash);

        $stmt->execute();

        $stmt->close();

        echo "Sign Up Success!";
        header("Location: main.php");
    }
    else {
        // Duplicate username, redirect to sign up page
        echo "Username already exists! Back to the Sign Up page in 2 seconds";
        header("Refresh: 2; url = login_signup.html");
    }
}
?>