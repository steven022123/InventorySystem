<?php include_once('dbconnect.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTER</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url(back.jpg);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            color: #ffe4e1;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: rgba(30, 30, 30, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            width: 300px;
        }

        p {
            margin: 10px 0;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 90%;
            padding: 10px;
            margin: 5px 0;
            border: 2px solid #333;
            border-radius: 5px;
            background-color: #2a2a2a;
            color: whitesmoke;
        }

        input[type="submit"] {
            background-color: #fa8128;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color:#ec9706;
        }

        a {
            color: #fa8128;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 10px;
        }

        a:hover {
            text-decoration: underline;
            color: #ec9706;
        }

        .message {
            text-align: center;
            margin-top: 20px;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <form action="" method="post">
        <p>Username: <input type="text" name="username" required></p>
        <p>Full Name: <input type="text" name="fullname" required></p>
        <p>Password: <input type="password" name="password" required></p>
        <p>Position: 
            <select name="position" required>
                
                <option value="Manager">Manager</option>
                
            </select>
        </p>
        <input type="submit" value="Register" name="register">
        <a href="login.php">Login</a>
    </form>

    <?php
    if (isset($_POST['register'])) {
        $user_name = $_POST['username'];
        $full_name = $_POST['fullname'];
        $password = $_POST['password'];
        $position = $_POST['position']; // Get the position from the form

        // Prepare the query using PDO
        $query = "INSERT INTO manager_register (username, fullname, password, position) VALUES (:username, :fullname, :password, :position)";
        $stmt = $pdo->prepare($query);

        // Bind parameters
        $stmt->bindParam(':username', $user_name);
        $stmt->bindParam(':fullname', $full_name);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':position', $position);  // Bind the position

        // Execute the query
        if ($stmt->execute()) {
            echo "<script>window.alert('Added Successfully'); window.location.href='login.php';</script>";
        } else {
            echo "<div class='message'>Error: Unable to register user.</div>";
        }
    }
    ?>
</body>
</html>
