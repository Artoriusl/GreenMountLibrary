<!DOCTYPE html> 
<html lang="en"> 
<head> 
<meta charset="UTF-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<title>Search For Books</title> 
<link rel="stylesheet" href="style.css"> 
</head> 
<body> 
<?php 
session_start(); 
    if (!isset($_SESSION['username'])) { 
        header("Location: login.php"); 
        exit(); 
    } 

    $username = $_SESSION['username']; 

    // db connection variables 
    $servername = "localhost"; 
    $dbuser = "root"; 
    $dbpass = ""; 
    $dbname = "webdevlibrary"; 

    $conn = new mysqli($servername, $dbuser, $dbpass, $dbname); 

    if ($conn->connect_error) { 
        die("Connection failed: " . $conn->connect_error); 
    } 

    // gets categories from the db 
    $catQuery = "SELECT categoryID, categoryName FROM categories ORDER BY categoryName ASC"; 
    $catResult = $conn->query($catQuery); 
    $categories = []; 
    while ($row = $catResult->fetch_assoc()) { 
        $categories[$row['categoryID']] = $row['categoryName']; 
    } 

    // search parameters 
    $titleSearch = isset($_GET['title']) ? $_GET['title'] : ""; 
    $authorSearch = isset($_GET['author']) ? $_GET['author'] : ""; 
    $categorySearch = isset($_GET['category']) ? $_GET['category'] : ""; 

    $sql = "SELECT * FROM books WHERE 1=1"; 

    if ($titleSearch !== "") { 
        $sql .= " AND bookTitle LIKE '%$titleSearch%'"; 
    } 

    if ($authorSearch !== "") { 
        $sql .= " AND author LIKE '%$authorSearch%'"; 
    } 

    if ($categorySearch !== "") { 
        $sql .= " AND categoryID = '$categorySearch'"; 
    } 

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

    <h2>Search for Books</h2> 

    <!-- search form --> 
    <form method="GET" action="searchForBooks.php"> 
        <label>Title (partial allowed):</label><br> 
        <input type="text" name="title" value="<?php echo htmlspecialchars($titleSearch); 
?>"><br><br> 

        <label>Author (partial allowed):</label><br> 
        <input type="text" name="author" value="<?php echo htmlspecialchars($authorSearch); 
?>"><br><br> 

        <label>Category:</label><br> 
        <select name="category"> 
            <option value="">-- Any Category --</option> 
            <?php 
            foreach ($categories as $id => $name) { 
                $selected = ($categorySearch == $id) ? "selected" : ""; 
                echo "<option value='$id' $selected>$name</option>"; 
            } 
            ?> 
        </select><br><br> 

        <button type="submit">Search</button> 
    </form> 

    <hr> 

    <h2>Search Results</h2> 

    <?php 
    $searchPerformed = ($titleSearch !== "" || $authorSearch !== "" || $categorySearch !== ""); 

    if (!$searchPerformed) { 
        // if the user hasn't searched, aka first time entering the page, it will display this 
message 
        echo "<p>Please enter a search term above.</p>"; 

    } else if ($result->num_rows == 0) { 
        echo "<p>No results found.</p>"; 
    } else { 

        while ($row = $result->fetch_assoc()) { 

            $ISBN = $row['ISBN']; 
            $title = $row['bookTitle']; 
            $author = $row['author']; 
            $edition = $row['edition']; 
            $year = $row['year']; 
            $categoryID = $row['categoryID']; 
            $reserved = $row['reserved']; 

            $categoryName = isset($categories[$categoryID]) ? $categories[$categoryID] : "Unknown"; 

            $imagePath = "book_images/" . $ISBN . ".jpg"; 

            echo "<div style='margin-bottom:20px; border-bottom:1px solid #ccc; padding-bottom:15px; 
display:flex;'>"; 

            if (file_exists($imagePath)) { 
                echo "<img src='$imagePath' style='width:120px; margin-right:15px;' alt='Book 
Cover'>"; 
            } else { 
                echo "<img src='book_images/default.jpg' style='width:120px; margin-right:15px;' 
alt='No Image'>"; 
            } // end if 

            echo "<div>"; 
            echo "<strong>Title:</strong> $title<br>"; 
            echo "<strong>Author:</strong> $author<br>"; 
            echo "<strong>Edition:</strong> $edition<br>"; 
            echo "<strong>Year:</strong> $year<br>"; 
            echo "<strong>Category:</strong> $categoryName<br>"; 
            echo "<strong>ISBN:</strong> $ISBN<br>"; 

            if ($reserved == 1) { 
                echo "<strong>Status:</strong> <span style='color:red;'>Reserved</span><br>"; 
            } else { 
                echo "<strong>Status:</strong> <span style='color:green;'>Available</span><br>"; 

                // reserve button if available 
                echo "<form method='POST' action='reserveBook.php' style='margin-top:10px;'>"; 
                echo "<input type='hidden' name='ISBN' value='$ISBN'>"; 
                echo "<button type='submit'>Reserve this book</button>"; 
                echo "</form>"; 
            } // end if 

            echo "</div>"; 
            echo "</div>"; 
        } // end while 
    } // end if 

    $conn->close(); 
    ?> 

</body> 

</html> 