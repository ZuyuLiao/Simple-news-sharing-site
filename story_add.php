<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <title>Add Your Story</title>
</head>
<body>
    <?php
    session_start();
    if(!isset($_SESSION['username'])) {
        echo "You are not logged in. Redirect to news page in 2 seconds.";
        header("Refresh: 2; url = main0.php");
    }
    else
        echo sprintf("<h1>Add Your Story Here, %s.</h1>", htmlentities($_SESSION['username']));
    ?>
<a href="main.php">Back to News Site</a><br/><br/><br/>
<div>
    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
        <input id="title" type="text" name="title" placeholder="Please enter the story title"><br/><br/>
        <input id="title2" type="text" name="link" placeholder="Please enter the associated link"><br/><br/>
        <textarea rows="30" cols="50" name="content"></textarea><br/>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
        <input type="submit" value="Submit" name="submit">
    </form>

    <?php
    require 'database.php';
    // validate login status
    if(!isset($_SESSION['username'])) {
        echo "You are not logged in. Redirect to news page in 2 seconds.";
        header("Refresh: 2; url = main0.php");
    }
    else {
        $username = $_SESSION['username'];
        if(isset($_POST['submit'])) {
            if(!isset($_POST['title'])) {
                echo "Invalid Title. Back to the add news page in 2 seconds.";
                header("Refresh: 2; url = story_add.php");
                exit;
            }
            else if(!isset($_POST['link']) || empty($_POST['link'])) {
                echo "Invalid Link. Back to the add news page in 2 seconds.";
                header("Refresh: 2; url = story_add.php");
                exit;
            }
            else if(!isset($_POST['content']) || empty($_POST['content'])) {
                echo "Invalid Content. Back to the add news page in 2 seconds.";
                header("Refresh: 2; url = story_add.php");
                exit;
            }
            else {
                $title = $_POST['title'];
                $link = $_POST['link'];
                $content = $_POST['content'];
                // CSRF token validate
                if(!hash_equals($_SESSION['token'], $_POST['token'])){
                    die("Request forgery detected");
                }
                // Inputs validated and then Insert them into stories database
                $stmt = $mysqli->prepare("insert into stories (username, title, content) values (?,?,?)");
                if(!$stmt) {
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }

                $stmt->bind_param('sss', $username, $title, $content);

                $stmt->execute();

                $stmt->close();

                // Get story id
                $stmt = $mysqli->prepare("select id from stories where username=? and title=? and content=?");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }

                $stmt->bind_param('sss', $username, $title, $content);

                $stmt->execute();

                $stmt->bind_result($id);

                $stmt->fetch();

                $stmt->close();

                // Add link to links database
                $stmt = $mysqli->prepare("insert into links (id, link) values (?,?)");
                if(!$stmt) {
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }

                $stmt->bind_param('is', $id, $link);

                $stmt->execute();

                $stmt->close();

                echo "<script>alert('Add Story Success!')</script>";
            }
        }
    }
    ?>
</div>
</body>
</html>