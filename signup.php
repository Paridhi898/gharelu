<?php

$conn = mysqli_connect("localhost", "root", "", "gharelu_db");

if (!$conn) {
    die("Connection Failed");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $citizenship_id = $_POST['citizenship_id'];
    $user_type = $_POST['user_type'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check password
    if ($password != $confirm_password) {
        echo "<script>
                alert('Passwords do not match.');
                window.history.back();
              </script>";
        exit();
    }

    // Check username
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($result) > 0) {
        echo "<script>
                alert('Username already exists.');
                window.history.back();
              </script>";
        exit();
    }

    // Check email
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($result) > 0) {
        echo "<script>
                alert('Email already exists.');
                window.history.back();
              </script>";
        exit();
    }

    // Check phone number
    $result = mysqli_query($conn, "SELECT * FROM users WHERE phone_number='$phone_number'");
    if (mysqli_num_rows($result) > 0) {
        echo "<script>
                alert('Phone number already exists.');
                window.history.back();
              </script>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $sql = "INSERT INTO users
            (full_name, username, email, phone_number, citizenship_id, user_type, password)
            VALUES
            ('$full_name', '$username', '$email', '$phone_number',
             '$citizenship_id', '$user_type', '$hashed_password')";

    if (mysqli_query($conn, $sql)) {
        header("Location: login.php");
        exit();
    } else {
        echo "Registration Failed: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Signup</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="register-container">

    <div class="top-section">

        <div class="icon">🏠</div>

        <h1>Join Gharelu</h1>

        <p>Create your account and start renting</p>

    </div>

    <div class="form-section">

        <form method="POST">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Enter full name" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter username" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" minlength="10" maxlength="10"  placeholder="98XXXXXXXX">
            </div>

            <div class="form-group">
                <label>Citizenship ID</label>
                <input type="text" name="citizenship_id" placeholder="Eg:04-01-73-02257" minlength="9" required>
            </div>

            <div class="form-group">
                <label>User Type</label>

                <select name="user_type" required>

                    <option value="">Select User Type</option>

                    <option value="tenant">Tenant</option>

                    <option value="landlord">Landlord</option>

                  

                </select>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password"  placeholder="Confirm your password">
            </div>

            <button type="submit" class="register-btn">
                REGISTER
            </button>

        </form>

        <div class="login-text">

            Already have an account?

            <a href="login.php">Login here</a>

        </div>

    </div>

</div>

</body>
</html>