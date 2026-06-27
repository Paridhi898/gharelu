<?php

session_start();

$conn = mysqli_connect("localhost", "root", "", "gharelu_db");

if (!$conn) {
    die("Connection Failed");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {

        $user = mysqli_fetch_assoc($result);

        // VERIFY PASSWORD

        if (password_verify($password, $user['password'])) {

            // STORE SESSION

            $_SESSION['user_id'] = $user['id'];

            $_SESSION['username'] = $user['username'];

            $_SESSION['user_type'] = $user['user_type'];

            // REDIRECT BASED ON ROLE

            if ($user['user_type'] == "admin") {

                header("Location: admin_dashboard.php");
            } elseif ($user['user_type'] == "landlord") {

                header("Location: owner_dashboard.php");
            } elseif ($user['user_type'] == "tenant") {

                header("Location: tenant_dashboard.php");
            }

            exit();
        } else {

            $error = "Incorrect Password";
        }
    } else {

        $error = "User Not Found";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="login-container">

        <div class="top-section">

            <div class="icon">🏠</div>

            <h1>Welcome Back</h1>

            <p>Login to your Gharelu account</p>

        </div>

        <div class="form-section">

            <?php

            if ($error != "") {

                echo "<div class='error-message'>$error</div>";
            }

            ?>

            <form method="POST">

                <div class="form-group">

                    <label>Username</label>

                    <input type="text" name="username" required>

                </div>

                <div class="form-group">

                    <label>Password</label>

                    <input type="password" name="password" required>

                </div>

                <button type="submit" class="login-btn">
                    LOGIN
                </button>

            </form>

            <div class="register-text">

                Don't have an account?

                <a href="signup.php">Register here</a>

            </div>

        </div>

    </div>

</body>

</html>