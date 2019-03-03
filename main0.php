<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <title>News Site</title>
</head>
<body>
<div id ="welcome">
    <h1>Welcome to the news site</h1>
    <!--Login & Sign Up Button-->
    <form action="login_signup.html" method="post">
        <input id="login_signup" type="submit" value="Login/Sign Up"/>
    </form>
</div><br/><br/><br/>

<!--Display all the news-->
<div>
    <table id="customers">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Actions</th>
        </tr>
        <?php
        require 'database.php';
        $stmt = $mysqli->prepare("select id, title, username from stories order by id desc");
        if(!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt->execute();

        $stmt->bind_result($id, $title, $username);

        while($stmt->fetch()){
            echo sprintf("
            \t<tr>\n
            \t\t<td>%s</td>\n
            \t\t<td>%s</td>\n
            \t\t<td>
                <!--View Button, navigate to the story view page-->
                <form action='view.php' method='post'>
                <input type='hidden' class='button' name='id' value='$id'>
                <input type='submit' class='button' value='View' name='view'/>
                </form>
                </td>\n
            \t</tr>\n",
                $title,
                $username
            );
        }
        $stmt->close();
        ?>
    </table>
</div>
</body>
</html>