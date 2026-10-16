<?php 
    session_start(); 

    // user has to be logged in to cancel their reservations 
    if (!isset($_SESSION['username'])) { 
        header("Location: login.php"); 
        exit(); 
    } // end if 

    // handles errors if no book is selected 
    if (!isset($_POST['ISBN'])) { 
        die("No book selected."); 
    } // end if 

    $username = $_SESSION['username']; 
    $ISBN = $_POST['ISBN']; 

    // db connection variables 
    $servername = "localhost"; 
    $dbuser = "root"; 
    $dbpass = ""; 
    $dbname = "webdevlibrary"; 

    $conn = new mysqli( 
        $servername,  
        $dbuser,  
        $dbpass,  
        $dbname 
    ); // end connection to db 

    if ($conn->connect_error) { 
        die("connection error: " . $conn->connect_error); 
    } // end if 

    // remove reservation from reservedBooks table 
    $deleteSql =    "DELETE FROM reservedBooks  
                    WHERE ISBN = '$ISBN'  
                    AND username = '$username'"; 

    $conn->query($deleteSql); 

    // updates book table to set reserved status as false -> "book is now available" 
    $updateSql = "UPDATE books SET reserved = 0 WHERE ISBN = '$ISBN'"; 
    $conn->query($updateSql); 

    // returns to reservedbooks 
    header("Location: reservedBooks.php"); 
    exit(); 
?>