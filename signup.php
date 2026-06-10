<?php

$conn = mysqli_connect("localhost", "root", "", "gharelu_db");

if(!$conn){
    die("Connection Failed");
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $citizenship_id = $_POST['citizenship_id'];
    $user_type = $_POST['user_type'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if($password != $confirm_password){

        echo "Passwords do not match";

    }else{

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users
        (full_name, username, email, phone_number, citizenship_id, user_type, password)

        VALUES

        ('$full_name', '$username', '$email', '$phone_number',
        '$citizenship_id', '$user_type', '$hashed_password')";

        if(mysqli_query($conn, $sql)){

            header("Location: login.php");
            exit();

        }else{

            echo "Registration Failed";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Signup</title>

    <link rel="stylesheet" href="style.css">

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
                <input type="text" name="full_name" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" required>
            </div>

            <div class="form-group">
                <label>Citizenship ID</label>
                <input type="text" name="citizenship_id" required>
            </div>

            <div class="form-group">
                <label>User Type</label>

                <select name="user_type" required>

                    <option value="">Select User Type</option>

                    <option value="tenant">Tenant</option>

                    <option value="landlord">Landlord</option>

                    <option value="admin">Admin</option>

                </select>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
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