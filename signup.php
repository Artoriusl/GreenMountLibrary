<!DOCTYPE html> 
<html lang="en"> 

<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Sign Up</title> 
    <link rel="stylesheet" href="style.css"> 
</head> 

<body> 
    <?php 
    session_start(); 

    if (isset($_POST['register'])) { 

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
    
        $username = trim($_POST['username']); 
        $password = trim($_POST['password']); 
        $confirm = trim($_POST['confirm_password']); 
        $firstName = trim($_POST['firstName']); 
        $surName = trim($_POST['surName']); 
        $addressLine1 = trim($_POST['addressLine1']); 
        $addressLine2 = trim($_POST['addressLine2']); 
        $city = trim($_POST['city']); 
        $telephone = trim($_POST['telephone']); 
        $mobile = trim($_POST['mobile']); 

        $errors = []; 

        // checks that all fields are filled 
        if (!$username || !$password || !$confirm || !$firstName || !$surName || !$addressLine1 || 
!$city || !$telephone || !$mobile) { 
            $errors[] = "All required fields must be filled."; 
        } // end if 
    
        // checks that password is exactly 6 characters long 
        if (strlen($password) !== 6) { 
            $errors[] = "Password must be exactly 6 characters long."; 
        } // end if 
    
        // password confirmation 
        if ($password !== $confirm) { 
            $errors[] = "Password and confirmation do not match."; 
        } // end if 
    
        // checks that telephone and mobile numbers are 10 digits long and NUMBERS!! 
        if (!ctype_digit($telephone) || strlen($telephone) !== 10) { 
            $errors[] = "Telephone must be 10 numeric digits."; 
        } // end telephone if 
        if (!ctype_digit($mobile) || strlen($mobile) !== 10) { 
            $errors[] = "Mobile must be 10 numeric digits."; 
        } // end mobile if 
    
        // checks if username isn't taken 
        $checkUser = "SELECT username FROM users WHERE username = '$username'"; 
        $result = $conn->query($checkUser); 
        if ($result->num_rows > 0) { 
            $errors[] = "Username already exists. Please choose another."; 
        } // end if 
    
        // if everything is alright, we create the user 
        if (empty($errors)) { 

            $sql = "INSERT INTO users ( 
                username,  
                password,  
                firstName,  
                surName,  
                addressLine1,  
                addressLine2,  
                city,  
                telephone,  
                mobile 
                ) 
            VALUES ( 
            '$username',  
            '$password',  
            '$firstName',  
            '$surName',  
            '$addressLine1',  
            '$addressLine2',  
            '$city',  
            '$telephone',  
            '$mobile' 
            )"; 

            // connection to db is successful we register the user and continue to the library 
catalogue 
            if ($conn->query($sql) === TRUE) { 
                $_SESSION['username'] = $username; 
                header("Location: catalogue.php"); 
                exit(); 
            } else { 
                $errors[] = "Error creating user: " . $conn->error; 
            } // end if 
        } 

        $conn->close(); 
    } // end if 
    ?> 

    <!-- html code for my sign up form --> 
    <h2>Register</h2> 

    <form method="POST" action=""> 
        <label>Username:</label><br> 
        <input type="text" name="username" required><br><br> 

        <label>Password (6 characters):</label><br> 
        <input type="password" name="password" required><br><br> 

        <label>Confirm Password:</label><br> 
        <input type="password" name="confirm_password" required><br><br> 

        <label>First Name:</label><br> 
        <input type="text" name="firstName" required><br><br> 

        <label>Surname:</label><br> 
        <input type="text" name="surName" required><br><br> 

        <label>Address Line 1:</label><br> 
        <input type="text" name="addressLine1" required><br><br> 

        <label>Address Line 2 (optional):</label><br> 
        <input type="text" name="addressLine2"><br><br> 

        <label>City:</label><br> 
        <input type="text" name="city" required><br><br> 

        <label>Telephone (10 digits):</label><br> 
        <input type="text" name="telephone" required><br><br> 

        <label>Mobile (10 digits):</label><br> 
        <input type="text" name="mobile" required><br><br> 

        <button type="submit" name="register">Register</button> 
    </form> 

    <!-- php code for error message if it fails --> 
    <?php 
    if (!empty($errors)) { 
        echo "<ul style='color:red;'>"; 
        foreach ($errors as $e) { 
            echo "<li>$e</li>"; 
        } 
        echo "</ul>"; 
    } 
    ?> 
</body> 

</html> 