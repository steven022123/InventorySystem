<?php include_once('dbconnect.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
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
            background-color: #ec9706;
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
        <p>Password: <input type="password" name="password" required></p>
        <p>Position: 
            <select name="position" required>
                
                <option value="Manager">Manager</option>
            </select>
        </p>
        
        <input type="submit" value="Login" name="login">
        <a href="register.php">Register</a>
    </form>

    <?php
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $position = $_POST['position'];

        // Prepare SQL query to prevent SQL injection
        $stmt = $pdo->prepare("SELECT * FROM manager_register WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $manager_login = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($manager_login) {
            // If using hashed passwords, replace with password_hash() and password_verify()
            if ($manager_login['password'] === $password) { // For plain text password check
                // Check if the user is a Manager
                if ($manager_login['position'] === 'Manager') {
                    // Record login activity
                    $stmt_activity = $pdo->prepare("INSERT INTO manager_login (user_id, username, login_time, success, position) VALUES (:user_id, :username, :login_time, :success, :position)");
                    $success = 1; // 1 for successful login
                    $stmt_activity->execute([
                        'user_id' => $manager_login['id'], // Assuming 'id' is the user's ID in the 'manager_login' table
                        'username' => $username,
                        'login_time' => date('Y-m-d H:i:s'),
                        'success' => $success,
                        'position' => $manager_login['position']
                    ]);
                    echo "<script>window.alert('Login Successfully'); window.location.href='inventory.php';</script>";
                } else {
                    // Record failed login attempt for non-managers
                    $stmt_activity = $pdo->prepare("INSERT INTO manager_login (user_id, username, login_time, success, position) VALUES (:user_id, :username, :login_time, :success, :position)");
                    $success = 0; // 0 for denied access
                    $stmt_activity->execute([
                        'user_id' => $manager_login['id'],
                        'username' => $username,
                        'login_time' => date('Y-m-d H:i:s'),
                        'success' => $success,
                        'position' => $manager_login['position']
                    ]);
                    echo "<script>window.alert('Access Denied: Only Managers can access the inventory.');</script>";
                }
            } else {
                // Record failed login attempt
                $stmt_activity = $pdo->prepare("INSERT INTO manager_login (user_id, username, login_time, success, position) VALUES (:user_id, :username, :login_time, :success, :position)");
                $success = 0; // 0 for incorrect password
                $stmt_activity->execute([
                    'user_id' => $manager_login['id'], // Log the user ID for record purposes
                    'username' => $username,
                    'login_time' => date('Y-m-d H:i:s'),
                    'success' => $success,
                    'position' => $manager_login['position']
                ]);
                echo "<script>window.alert('Incorrect Password');</script>";
            }
        } else {
            // Record failed login attempt
            $stmt_activity = $pdo->prepare("INSERT INTO manager_login(user_id, username, login_time, success, position) VALUES (:user_id, :username, :login_time, :success, :position)");
            $success = 0; // 0 for user not found
            $stmt_activity->execute([
                'user_id' => null, // No user ID available
                'username' => $username,
                'login_time' => date('Y-m-d H:i:s'),
                'success' => $success,
                'position' => $position
            ]);
            echo "<script>window.alert('User not found or incorrect position');</script>";
        }
    }
    ?>
</body>
</html>
