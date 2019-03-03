<?php
session_start();
if(isset($_POST['logout'])) {
    session_destroy();
    echo "Logout Success!";
    header("Location: main0.php");
}
?>