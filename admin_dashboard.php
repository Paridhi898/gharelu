<?php
require_once __DIR__ . '/config.php';

require_role('admin');

$conn = db_connect();

if (isset($_GET['approve']) || isset($_GET['reject'])) {
    $id = intval($_GET['approve'] ?? $_GET['reject']);
    $status = isset($_GET['approve']) ? 'verified' : 'rejected';

    $stmt = mysqli_prepare($conn, 'UPDATE landlord SET verification_status = ? WHERE user_id = ?');
    mysqli_stmt_bind_param($stmt, 'si', $status, $id);
    mysqli_stmt_execute($stmt);

    header('Location: admin_dashboard.php');
    exit();
}

$totalUsers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$totalLandlords = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE user_type='landlord'"))[0];
$totalTenants = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE user_type='tenant'"))[0];

$pending = mysqli_query($conn, "SELECT u.id, u.username, u.full_name, u.email, u.phone_number, u.citizenship_id, l.verification_status FROM users u JOIN landlord l ON l.user_id = u.id WHERE u.user_type='landlord' AND l.verification_status != 'verified'");
$pendingCount = mysqli_num_rows($pending);

mysqli_close($conn);

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
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Citizenship ID</th>
                            <th>Status</th>
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
        <?php echo htmlspecialchars($row['username']); ?>
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
    <td>
        <?php echo ucfirst(htmlspecialchars($row['verification_status'])); ?>
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