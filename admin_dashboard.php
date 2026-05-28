<?php

session_start();

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
    exit();
}

if($_SESSION['user_type'] != "admin"){

    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <title>Admin Dashboard</title>

</head>

<body>

    <h1>Admin Dashboard</h1>

    <h2>
        Welcome <?php echo $_SESSION['username']; ?>
    </h2>

    <ul>

        <li>Manage Users</li>

        <li>Manage Properties</li>

        <li>Approve Listings</li>

        <li>View Reports</li>

    </ul>

    <a href="logout.php">Logout</a>

</body>
</html>