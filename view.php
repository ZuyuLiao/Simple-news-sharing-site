<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <title>Story</title>
</head>
<body id="b">
<?php
require 'database.php';
session_start();
if(isset($_SESSION['username'])) {
    echo "<a href='main.php'>Back to News Site</a><br/><br/>";
}
else {
    echo "<a href='main0.php'>Back to News Site</a><br/><br/>";
}
if(isset($_POST['view'])) {
    $id = $_POST['id'];
    // Retrieve story and link data
    $stmt = $mysqli->prepare("select username, title, content, links.link from stories join links on(stories.id = links.id) where stories.id=?");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->bind_param('d', $id);

    $stmt->execute();

    $stmt->bind_result($username, $title, $content, $link);

    $stmt->fetch();

    $stmt->close();

    // Display the story
    echo sprintf("
        <p id='words3'>%s</p>
        <a href='$link' target='_blank'>Link</a>
        <p id = 'words'>Author: %s</p>
        <textarea rows='50' cols='100' readonly='readonly' id ='text'>%s</textarea><br/>",
        htmlspecialchars($title), htmlspecialchars($username), htmlspecialchars($content));

    // If ur a login user, then show the comment submit form and a button to refresh current page to load new comment

    echo '<br/>';
    echo '<br/>';
    echo '<br/>';
    echo '<br/>';

    if(isset($_SESSION['username'])) {
        echo sprintf("
            <form action='comment_add.php' target='_blank' method='post'>
            <textarea rows='5' cols='50' name='comment' placeholder='Comment here!'></textarea>
            <input type='hidden' name='id' value='$id'>
            <input type='hidden' name='token' value='".$_SESSION['token']."'/>
            <input type='submit' value='Submit' name='submit'> 
            </form>
            <form action='view.php' method='post'>
            <input type='hidden' name='id' value='$id'/>
            <br/>
            Click here to refresh comments after submission
            <input type='submit' value='Refresh' name='view'/>
            </form><br/><br/><br/>
        ");
    }

    // Display comments

    $stmt = $mysqli->prepare("select username, content, c_id from comments where id=? order by c_id desc");
    if(!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->bind_param('i', $id);

    $stmt->execute();

    $stmt->bind_result($username, $comment, $c_id);


    echo "<p id='words3'> Comments</p>";
    echo "<table id = 'comment'>";
    while($stmt->fetch()) {
        // Grant edit & delete function to comment
        if(isset($_SESSION['username']) && $_SESSION['username'] == $username) {
            if(isset($_SESSION['token']))
                $token = $_SESSION['token'];
            echo sprintf("
                \t<tr>\n
                \t\t<td>%s</td>\n
                \t\t<td><textarea rows='3' cols='30' readonly='readonly' id ='text2'>%s</textarea></td>\n
                \t\t<td>
                    <!--Edit Button, navigate to the comment edit page-->
                    <form action='comment_edit.php' target='_blank' method='post'>
                    <input type='hidden' name='c_id' value='$c_id'/>
                    <input type='hidden' name='token' value='$token'/>
                    <input type='submit' value='Edit' name='edit'/>
                    </form>
                    <!--Delete Button-->
                    <form action='comment_delete.php' target='_blank' method='post'>
                    <input type='hidden' name='c_id' value='$c_id'/>
                    <input type='hidden' name='token' value='$token'/>
                    <input type='submit' value='Delete' name='delete'/>
                    </form>
                </td>\n
                \t</tr>\t
            ", $username, $comment);
        }
        else {
            echo sprintf("
                \t<tr>\n
                \t\t<td>%s</td>\n
                \t\t<td><textarea rows='3' cols='30' readonly='readonly' id = 'text2'>%s</textarea></td>\n
                \t</tr>\t
            ", $username, $comment);
        }
    }
    echo "</table>";
}
?>
</body>
</html>