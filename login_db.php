<?php
require 'database.php';

if(!isset($_POST['username']) || !preg_match('/^[\w_\-]+$/', $_POST['username'])) {
    echo "Invalid Username. Back to the login page in 2 seconds.";
    header("Refresh: 2; url = login_signup.html");
    exit;
}
else if(!isset($_POST['password']) || !preg_match('/^[\w_\-]+$/', $_POST['password'])) {
    echo "Invalid Password. Back to the login page in 2 seconds.";
    header("Refresh: 2; url = login_signup.html");
    exit;
}
else {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // select the users database to authenticate password
    $stmt = $mysqli->prepare("select password from users where username = ?");
    if(!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->bind_param('s', $username);

    $stmt->execute();

    $stmt->bind_result($pwd_hash);
    $stmt->fetch();

    if(password_verify($password, $pwd_hash)) {
        // Login Succeeded!
        session_start();
        $_SESSION['username'] = $username;
        // Create CSRF token
        $_SESSION['token'] = bin2hex(random_bytes(32));
        header("Location: main.php");
    }
    else {
        // Login Failed!
        echo "Login Failed. Redirect to login page in 2 seconds.";
        header("Refresh: 2; url = login_signup.html");
    }
}
?>