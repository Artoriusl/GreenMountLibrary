<!-- this php code logs the user out when clicking the logout option at the top of the nav bar--> 
<?php 
    session_start(); 
    session_unset(); 
    session_destroy(); 

    header("Location: login.php"); 
    exit(); 
?> 