<!DOCTYPE html> 
<html lang="en"> 

<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Green Mount Library Catalogue</title> 
    <link rel="stylesheet" href="style.css"> 
</head> 

<body> 

    <?php 
    session_start(); 

    // user has to be logged in to access the catalogue 
    if (!isset($_SESSION['username'])) { 
        header("Location: login.php"); 
        exit(); 
    } 

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
        die("Connection failed: " . $conn->connect_error); 
    } // end if 
    
    // settings for the number of books displayed per page 
    $booksPerPage = 5; 
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1; 
    $start = ($page - 1) * $booksPerPage; 

    // categories bames based on categoryID 
    $categories = [ 
        1 => "Science Fiction", 
        2 => "Fantasy", 
        3 => "Psychology", 
        4 => "Mystery", 
        5 => "Programming" 
    ]; // end categories 
    
    // counts how many books are there to divide for page settings 
    $countSql = "SELECT COUNT(*) AS total FROM books"; 
    $countResult = $conn->query($countSql); 
    $totalBooks = $countResult->fetch_assoc()['total']; 
    $totalPages = ceil($totalBooks / $booksPerPage); 

    // retrieves books for the current page 
    $sql = "SELECT * FROM books LIMIT $start, $booksPerPage"; 
    $result = $conn->query($sql); 

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

    <h2>Book Catalogue</h2> 

    <?php 
    if ($result->num_rows > 0) { 
        while ($row = $result->fetch_assoc()) { 
            $ISBN = $row['ISBN']; 
            $title = $row['bookTitle']; 
            $author = $row['author']; 
            $edition = $row['edition']; 
            $year = $row['year']; 
            $categoryID = $row['categoryID']; 
            $reserved = $row['reserved']; 

            // retrieves category name based on the categoryID from the db 
            $categoryName = isset($categories[$categoryID]) ? $categories[$categoryID] : "Unknown"; 

            // Book image path (explained below) 
            $imagePath = "book_images/" . $ISBN . ".jpg"; 

            echo "<div style='margin-bottom:20px; border-bottom:1px solid #ccc; padding-bottom:15px; 
display:flex;'>"; 

            // displays book images if they exist, otherwise displays the default image 
            if (file_exists($imagePath)) { 
                echo "<img src='$imagePath' style='width:120px; margin-right:15px;' alt='Book 
Cover'>"; 
            } else { 
                echo "<img src='book_images/default.jpg' style='width:120px; margin-right:15px;' 
alt='No Image'>"; 
            } 

            // displays book details 
            echo "<div>"; 
            echo "<strong>Title:</strong> $title<br>"; 
            echo "<strong>Author:</strong> $author<br>"; 
            echo "<strong>Edition:</strong> $edition<br>"; 
            echo "<strong>Year:</strong> $year<br>"; 
            echo "<strong>Category:</strong> $categoryName<br>"; 
            echo "<strong>ISBN:</strong> $ISBN<br>"; 

            // shows reservation status 
            if ($reserved == 1) { 
                echo "<strong>Status:</strong> <span style='color:red;'>Reserved</span><br>"; 
            } else { 
                echo "<strong>Status:</strong> <span style='color:green;'>Available</span><br>"; 
            } // end if 
    
            // button to reserve book if available 
            if ($reserved == 0) { 
                echo "<form method='POST' action='reservedBooks.php' style='margin-top:10px;'>"; 
                echo "<input type='hidden' name='ISBN' value='$ISBN'>"; 
                echo "<button type='submit'>Reserve this book</button>"; 
                echo "</form>"; 
            } // end if 
    
            echo "</div>"; 
            echo "</div>"; 
        } // end while 
    } else { 
        echo "No books found."; 
    } // end if 
    
    // links for other pages 
    echo "<div style='margin-top:20px;'>"; 

    for ($i = 1; $i <= $totalPages; $i++) { 
        if ($i == $page) { 
            echo "<strong>$i</strong> "; 
        } else { 
            echo "<a href='catalogue.php?page=$i'>$i</a> "; 
        } // end if 
    } // end for 
echo "</div>"; 
$conn->close(); 
?> 
</body> 
</html> 