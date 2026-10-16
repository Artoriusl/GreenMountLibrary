<!DOCTYPE html> 
<html lang="en"> 
<head> 
<meta charset="UTF-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<title>Reserved Books</title> 
<link rel="stylesheet" href="style.css"> 
</head> 
<body> 
    <?php 
    session_start(); 

    // user has to be logged in to access their reserved books 
    if (!isset($_SESSION['username'])) { 
        header("Location: login.php"); 
        exit(); 
    } // end if 
    
    $username = $_SESSION['username']; 

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
    ); // end of connection to db 
    
    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } // end if 
    
    // retrieves books reserved by the user who's logged in 
    $sql = "SELECT r.ISBN, r.reservedDate, b.bookTitle, b.author, b.year, b.edition, b.categoryID 
        FROM reservedBooks r 
        JOIN books b ON r.ISBN = b.ISBN 
        WHERE r.username = '$username' 
        ORDER BY r.reservedDate DESC"; 

    $result = $conn->query($sql); 

    // category names based on categoryID 
    $categories = [ 
        1 => "Science Fiction", 
        2 => "Fantasy", 
        3 => "Psychology", 
        4 => "Mystery", 
        5 => "Programming" 
    ]; 

    ?> 

    <!-- html for nav bar --> 
    <div class="navbar"> 

        <a href="catalogue.php"> 
            <img src="greenMountainLogo.png" alt="Logo"> 
        </a> 

        <a href="catalogue.php" class="nav-title">Green Mount Library</a> 

        <div class="nav-links"> 
            <a href="catalogue.php">Catalogue</a> 
            <a href="reservedBooks.php">Reserved Books</a> 
            <a href="searchForBooks.php">Search</a> 
            <a href="logout.php">Logout</a> 
        </div> 

    </div> 

    <h2>Your Reserved Books</h2> 

    <?php 
    // if no reservations 
    if ($result->num_rows == 0) { 
        echo "<p>You have no reserved books.</p>"; 
    } else { 

        while ($row = $result->fetch_assoc()) { 

            $ISBN = $row['ISBN']; 
            $title = $row['bookTitle']; 
            $author = $row['author']; 
            $year = $row['year']; 
            $edition = $row['edition']; 
            $catID = $row['categoryID']; 
            $date = $row['reservedDate']; 

            $category = isset($categories[$catID]) ? $categories[$catID] : "Unknown"; 

            echo "<div style='margin-bottom:20px; border-bottom:1px solid #ccc; padding
bottom:10px;'>"; 

            echo "<strong>Title:</strong> $title<br>"; 
            echo "<strong>Author:</strong> $author<br>"; 
            echo "<strong>Edition:</strong> $edition<br>"; 
            echo "<strong>Year:</strong> $year<br>"; 
            echo "<strong>Category:</strong> $category<br>"; 
            echo "<strong>ISBN:</strong> $ISBN<br>"; 
            echo "<strong>Reserved On:</strong> $date<br>"; 

            // this button cancels the reservation 
            echo "<form method='POST' action='cancelReservation.php' style='margin-top:10px;'>"; 
            echo "<input type='hidden' name='ISBN' value='$ISBN'>"; 
            echo "<button type='submit'>Cancel Reservation</button>"; 
            echo "</form>"; 

            echo "</div>"; 

        } // end while 
    } // end if 
    
$conn->close(); 
?> 
</body> 
</html> 