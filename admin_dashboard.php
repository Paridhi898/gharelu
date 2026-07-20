<?php
require_once __DIR__ . '/config.php';

require_role('admin');

$conn = db_connect();

$notice = trim($_GET['notice'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['user_id'] ?? 0);

    if ($id > 0 && ($action === 'approve' || $action === 'reject')) {
        $status = $action === 'approve' ? 'verified' : 'rejected';
        
        // Update landlord table
        $stmt1 = mysqli_prepare($conn, 'UPDATE landlord SET verification_status = ?, verified_at = NOW() WHERE user_id = ?');
        if ($stmt1) {
            mysqli_stmt_bind_param($stmt1, 'si', $status, $id);
            mysqli_stmt_execute($stmt1);
        }
        
        // Update users table
        $stmt2 = mysqli_prepare($conn, 'UPDATE users SET verification_status = ?, verified_at = NOW() WHERE id = ?');
        if ($stmt2) {
            mysqli_stmt_bind_param($stmt2, 'si', $status, $id);
            mysqli_stmt_execute($stmt2);
        }

        $notice = $action === 'approve' ? 'Landlord verified successfully.' : 'Landlord rejected successfully.';
        header('Location: admin_dashboard.php?notice=' . urlencode($notice));
        exit();
    }
}

$totalUsers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$totalProperties = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM house"))[0];
$totalLandlords = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE user_type='landlord'"))[0];
$totalTenants = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE user_type='tenant'"))[0];
$totalReviews = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM review"))[0];

$pending = mysqli_query($conn, "
    SELECT u.id, u.username, u.full_name, u.phone_number, u.citizenship_id, u.created_at 
    FROM users u 
    INNER JOIN landlord l ON u.id = l.user_id 
    WHERE u.user_type='landlord' AND l.verification_status='pending' 
    ORDER BY u.id DESC
");
$pendingCount = mysqli_num_rows($pending);

mysqli_close($conn);

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>🏠Gharelu Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_theme.css">

</head>

<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin_dashboard.php">
            🏡 Gharelu
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="admin_dashboard.php">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_users.php">
                        <i class="fas fa-users me-1"></i>Manage Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_listings.php">
                        <i class="fas fa-home me-1"></i>Manage Listings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_reviews.php">
                        <i class="fas fa-star me-1"></i>Manage Reviews
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Welcome Section -->
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Welcome, Admin!</h2>
        </div>
    </div>
</div>

<!-- Notification Area -->
<?php if ($notice !== ''): ?>
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($notice); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

<!-- Statistics Cards Section -->
<div class="container mt-4">
    <div class="row">
        <!-- Total Users Card - Blue Gradient -->
        <div class="col-md-3 mb-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Users</h5>
                            <h2 class="display-4"><?php echo $totalUsers; ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Properties Card - Green Gradient -->
        <div class="col-md-3 mb-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Properties</h5>
                            <h2 class="display-4"><?php echo $totalProperties; ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Reviews Card - Orange Gradient -->
        <div class="col-md-3 mb-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Reviews</h5>
                            <h2 class="display-4"><?php echo $totalReviews; ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Landlords Card - Yellow/Orange Gradient -->
        <div class="col-md-3 mb-4">
            <div class="card text-white" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Pending Landlords</h5>
                            <h2 class="display-4"><?php echo $pendingCount; ?></h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Landlord Verification Panel -->
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0"><i class="fas fa-user-check me-2"></i>Pending Landlord Verification</h4>
        </div>
        <div class="card-body">
            <?php if ($pendingCount == 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No pending landlord verifications.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Phone Number</th>
                                <th>Registration Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($pending)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?php echo intval($row['id']); ?>">
                                            <button type="submit" name="action" value="approve" class="btn btn-success btn-sm me-1">
                                                <i class="fas fa-check me-1"></i>Verify Landlord
                                            </button>
                                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this landlord?');">
                                                <i class="fas fa-times me-1"></i>Reject Landlord
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="container mt-5 mb-4">
    <div class="text-center text-muted">
        <small>&copy; 2026 Gharelu House Rental System. All rights reserved.</small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
