<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <title>Edit Your Story</title>
</head>
<body>
<?php
session_start();
require 'database.php';
if(!isset($_SESSION['username'])) {
    echo "You are not logged in. Redirect to news page in 2 seconds.";
    header("Refresh: 2; url = main0.php");
}
else {
    echo sprintf("<h1>Edit Your Story Here, %s.</h1>", htmlentities($_SESSION['username']));
    if(isset($_POST['edit'])) {
        $id = $_POST['id'];
        // select the story which needs to be edit
        $stmt = $mysqli->prepare("select title, username, content from stories where id=?");
        if(!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt->bind_param('i', $id);

        $stmt->execute();

        $stmt->bind_result($title, $username, $content);

        $stmt->fetch();

        $stmt->close();
    }
}
if(isset($_SESSION['token'])) {
    $token = $_SESSION['token'];
    echo sprintf("
    <a href='main.php'>Back to News Site</a><br/><br/><br/>
    <div>
        <form action='story_edit_db.php' method='post'>
        <input id='title' type='text' name='title' value='$title'><br/><br/>
        <textarea rows='30' cols='50' name='content'>$content</textarea><br/>
        <input type='hidden' name='id' value='$id'/> 
        <input type='hidden' name='token' value='$token'/>
        <input type='submit' value='Submit' name='submit'/>
        </form>
    </div>
");
}
?>
</body>
</html>