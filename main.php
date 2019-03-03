<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <title>News Site</title>
</head>
<body>
<div>
    <?php
    session_start();
    if(!isset($_SESSION['username'])) {
        echo "You are not logged in. Redirect to news page in 2 seconds.";
        header("Refresh: 2; url = main0.php");
    }
    else
        echo sprintf("<div id='welcome'>Welcome to the news site, %s.</div>", htmlentities($_SESSION['username']));
    ?>
    <br/>
    <br/>
    <p id="words2"> If you want delete your account, click below.</p>
    <!--User Delete Button-->
    <form action="user_delete.php" method="post">
        <input type="hidden" class='button' name="token" value="<?php echo $_SESSION['token'];?>">
        <input type="submit" class='button' name="delete" value="Delete Your Account"/>
    </form>
    
    <br/>
    <p id="words22"> If you want to log out, click below</p>
    <!--Logout Button-->
    <form action="logout.php" method="post">
        <input class='button' type="submit" name="logout" value="Logout"/>
    </form>
    
</div>
<br/>

<br/>
<p id="words23"> If you want to add news story, click below</p>
<!--Add-News-Button, navigate to the stories-adding page-->
<form action="story_add.php" method="post">
    <input class='button' type="submit" value="Add News"/>
</form>

<br/>
<br/>

<p id ="words24"> Click the link below to check your favorites!</p>
<!--A link navigate to My Favorites-->
<a href="favorites.php" target="_blank">My Favorites</a>
<br/><br/>

<p id ="words25"> Here is the all news story!</p>
<!--Display all the news-->
<div>
    <table id="customers1">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Actions</th>
        </tr>
        <?php
        require 'database.php';
        if(isset($_SESSION['token']))
            $token = $_SESSION['token'];
        $stmt = $mysqli->prepare("select id, title, username from stories order by id desc");
        if(!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt->execute();

        $stmt->bind_result($id, $title, $username);

        $user_curr = $_SESSION['username'];
        while($stmt->fetch()){
            if($user_curr != $username) {
                // The current user isn't the author, then only show view & like button.
                echo sprintf("
                    \t<tr>\n
                    \t\t<td>%s</td>\n
                    \t\t<td>%s</td>\n
                    \t\t<td>
                        <!--View Button, navigate to the story & comment view page-->
                        <form action='view.php' method='post'>
                        <input type='hidden' class='button' name='id' value='$id'/>
                        <input type='submit' class='button' value='View' name='view'/>
                        </form>
                        <!--Like Button, add this story to my favorites-->
                        <form action='like.php' target='_blank' method='post'>
                        <input type='hidden' class='button' name='id' value='$id'/>
                        <input type='hidden' class='button' name='token' value='$token'/>
                        <input type='submit' class='button' value='Like' name='like'/>
                        </form>
                    </td>\n
                    \t</tr>",
                    $title,
                    $username
                );
            }
            else {
                // The current user is the author, show view, like, edit, delete button
                if(isset($_SESSION['token']))
                    $token = $_SESSION['token'];
                echo sprintf("
                    \t<tr>\n
                    \t\t<td>%s</td>\n
                    \t\t<td>%s</td>\n
                    \t\t<td>
                        <!--View Button, navigate to the story & comment view page-->
                        <form action='view.php' method='post'>
                        <input type='hidden' class='button' name='id' value='$id'/>
                        <input type='submit' class='button' value='View' name='view'/>
                        </form>

                        <!--Like Button, add this story to my favorites-->
                        <form action='like.php' target='_blank' method='post'>
                        <input type='hidden' class='button' name='id' value='$id'/>
                        <input type='hidden' class='button' name='token' value='$token'/>
                        <input type='submit' class='button' value='Like' name='like'/>
                        </form>
                        
                        <!--Edit Button, navigate to the story edit page-->
                        <form action='story_edit.php' method='post'>
                        <input type='hidden' class='button' name='id' value='$id'/>
                        <input type='hidden' class='button' name='token' value='$token'/>
                        <input type='submit' class='button' value='Edit' name='edit'/>
                        </form>

                        <!--Delete Button-->
                        <form action='story_delete.php' method='post'>
                        <input type='hidden' class='button' name='id' value='$id'/>
                        <input type='hidden' class='button' name='token' value='$token'/>
                        <input type='submit' class='button' value='Delete' name='delete'/>
                        </form>
                        </td>\n
                    \t</tr>",
                    $title,
                    $username
                );
            }
        }
        $stmt->close();
        ?>
    </table>
</div>
</body>
</html>