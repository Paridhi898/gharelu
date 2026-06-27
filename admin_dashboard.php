<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['user_type'] != "admin") {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "gharelu_db");

if (!$conn) {
    die("Connection Failed");
}

/* Approve Landlord */
if (isset($_GET['approve'])) {

    $id = intval($_GET['approve']);

    mysqli_query($conn, "
        UPDATE users
        SET verification_status='verified',
            verified_at=NOW()
        WHERE id='$id'
    ");

    header("Location: admin_dashboard.php");
    exit();
}

/* Reject Landlord */
if (isset($_GET['reject'])) {

    $id = intval($_GET['reject']);

    mysqli_query($conn, "
        UPDATE users
        SET verification_status='rejected'
        WHERE id='$id'
    ");

    header("Location: admin_dashboard.php");
    exit();
}

/* Dashboard Counts */

$totalUsers = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users"));

$totalLandlords = mysqli_num_rows(mysqli_query($conn, "
SELECT * FROM users
WHERE user_type='landlord'
"));

$totalTenants = mysqli_num_rows(mysqli_query($conn, "
SELECT * FROM users
WHERE user_type='tenant'
"));

$pending = mysqli_query($conn, "
SELECT *
FROM users
WHERE user_type='landlord'
AND verification_status='pending'
");

$pendingCount = mysqli_num_rows($pending);

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Gharelu Admin Dashboard</title>

    <link rel="stylesheet" href="css/admin.css">

</head>

<body>

    <header>

        <div class="logo">

            🏠 <span>Gharelu</span>

        </div>

        <nav>

            <a href="admin_dashboard.php">Dashboard</a>


            <a href="logout.php">Logout</a>

        </nav>

    </header>


    <div class="container">

        <h1>Admin Dashboard</h1>

        <p>Welcome, System Administrator!</p>


        <div class="cards">

            <div class="card blue">

                <h3>Total Users</h3>

                <h1><?php echo $totalUsers; ?></h1>

            </div>


            <div class="card blue">

                <h3>Landlords</h3>

                <h1><?php echo $totalLandlords; ?></h1>

            </div>


            <div class="card blue">

                <h3>Tenants</h3>

                <h1><?php echo $totalTenants; ?></h1>

            </div>


            <div class="card blue">

                <h3>Pending Verification</h3>

                <h1><?php echo $pendingCount; ?></h1>

            </div>

        </div>



        <div class="verification">

            <div class="verification-header">

                <h2>Pending Landlord Verification</h2>

            </div>


            <div class="verification-body">

                <?php

                if ($pendingCount == 0) {

                    echo "<p>No pending landlord verifications.</p>";
                } else {

                ?>

                    <table>

                        <tr>

                            <th>Name</th>

                            <th>Email</th>

                            <th>Phone</th>

                            <th>Citizenship ID</th>
                            <th class="action-col">Action</th>

                        </tr>

                        <?php

                        while ($row = mysqli_fetch_assoc($pending)) {

                        ?>

                           <tr>

    <td>
        <?php echo htmlspecialchars($row['full_name']); ?>
    </td>

    <td>
        <?php echo htmlspecialchars($row['email']); ?>
    </td>

    <td>
        <?php echo htmlspecialchars($row['phone_number']); ?>
    </td>

    <td>
        <?php echo htmlspecialchars($row['citizenship_id']); ?>
    </td>

    <td class="action-col">

        <a href="admin_dashboard.php?approve=<?php echo $row['id']; ?>">
            <button type="button" class="approve-btn">
                Approve
            </button>
        </a>

        <a href="admin_dashboard.php?reject=<?php echo $row['id']; ?>">
            <button type="button" class="reject-btn">
                Reject
            </button>
        </a>

    </td>

</tr>
                        <?php

                        }

                        ?>

                    </table>

                <?php

                }

                ?>

            </div>

        </div>

    </div>

</body>

</html>