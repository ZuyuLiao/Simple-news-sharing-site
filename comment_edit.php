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
    echo sprintf("<h1>Edit Your Comment Here, %s.</h1>", htmlentities($_SESSION['username']));
    if(isset($_POST['edit'])) {
        $c_id = $_POST['c_id'];
        // select the comment which needs to be edit
        $stmt = $mysqli->prepare("select username, content from comments where c_id=?");
        if(!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt->bind_param('i', $c_id);

        $stmt->execute();

        $stmt->bind_result($username, $content);

        $stmt->fetch();

        $stmt->close();
    }
}
if(isset($_SESSION['token'])) {
    $token = $_SESSION['token'];
}

if(isset($_POST['submit'])) {
    if(!isset($_POST['content']) || empty($_POST['content'])) {
        echo "Invalid Commnet Content. Back to the add news page in 2 seconds.";
        header("Refresh: 2; url = comment_edit.php");
        exit;
    }
    else {
        $c_id = $_POST['c_id'];
        $comment = $_POST['content'];
        // CSRF token validate
        if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }
        // Inputs validated and then update them into comments database
        $stmt = $mysqli->prepare("update comments set content=? where c_id =?");
        if(!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt->bind_param('si', $comment, $c_id);

        $stmt->execute();

        $stmt->close();

        echo "<script>alert('Edit your comment Success!')</script>";
        echo "<script>window.close()</script>";
    }
}
?>
<a href='main.php'>Back to News Site</a><br/><br/><br/>
<div>
    <form action='<?php echo htmlentities($_SERVER['PHP_SELF']); ?>' method='post'>
        <textarea rows='5' cols='50' name='content'><?php echo $content; ?></textarea><br/>
        <input type='hidden' name='c_id' value='<?php echo $c_id; ?>'/>
        <input type='hidden' name='token' value='<?php echo $token; ?>'/>
        <input type='submit' value='Submit' name='submit'/>
    </form>
</div>
</body>
</html>