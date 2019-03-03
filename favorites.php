<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <title>My Favorites</title>
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
        echo sprintf("<h1>%s's Favorites.</h1>", htmlentities($_SESSION['username']));
    ?>
</div><br/>

<!--Display My Favorites-->
<div>
    <table id="customers">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Actions</th>
        </tr>
        <?php
        require 'database.php';

        $stmt = $mysqli->prepare("select stories.id, title, stories.username from stories join favorites on (stories.id = favorites.id) where favorites.username = ?");

        if(!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $user_curr = $_SESSION['username'];
        $stmt->bind_param('s', $user_curr);

        $stmt->execute();

        $stmt->bind_result($id, $title, $username);

        if(isset($_SESSION['token']))
            $token = $_SESSION['token'];

        while($stmt->fetch()){
                echo sprintf("
                    \t<tr>\n
                    \t\t<td>%s</td>\n
                    \t\t<td>%s</td>\n
                    \t\t<td>
                        <!--View Button, navigate to the story & comment view page-->
                        <form action='view.php' target='_blank' method='post'>
                        <input type='hidden' class='button' name='id' value='$id'/>
                        <input type='submit' class='button' value='View' name='view'/>
                        </form>
                        <!--Delete Button, delete it from my favorites-->
                        <form action='favorites_delete.php' method='post'>
                        <input type='hidden' class='button' name='id' value='$id'/>
                        <input type='hidden' class='button' name='token' value='$token'>
                        <input type='submit' class='button' value='Delete' name='delete'/>
                        </form>
                    </td>\n",
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