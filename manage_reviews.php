<?php
require_once __DIR__ . '/config.php';

require_role('admin');

$conn = db_connect();

$notice = trim($_GET['notice'] ?? '');
$error = trim($_GET['error'] ?? '');

// Handle Actions (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $reviewId = intval($_POST['review_id'] ?? 0);
        if ($reviewId > 0) {
            $stmt = mysqli_prepare($conn, 'DELETE FROM review WHERE review_id = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $reviewId);
                if (mysqli_stmt_execute($stmt)) {
                    $notice = 'Review deleted successfully.';
                } else {
                    $error = 'Failed to execute review delete.';
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = 'Failed to prepare review delete statement.';
            }
        }
        header('Location: manage_reviews.php?notice=' . urlencode($notice) . '&error=' . urlencode($error));
        exit();
    }
}

// Fetch all reviews with tenant and house info
$query = "
    SELECT r.*, h.title AS house_title, u.full_name AS tenant_name
    FROM review r
    LEFT JOIN house h ON r.house_id = h.house_id
    LEFT JOIN users u ON r.tenant_id = u.id
    ORDER BY r.review_id DESC
";
$reviewsResult = mysqli_query($conn, $query);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - Gharelu</title>
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
                    <a class="nav-link" href="admin_dashboard.php">
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
                    <a class="nav-link active" href="manage_reviews.php">
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

<!-- Page Header Section -->
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4"><i class="fas fa-star-half-alt me-2"></i>Manage Reviews</h2>
        </div>
    </div>
</div>

<!-- Alert Notifications -->
<?php if ($notice !== ''): ?>
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($notice); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>
<?php if ($error !== ''): ?>
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

<!-- Reviews Table -->
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0"><i class="fas fa-comments me-2"></i>Overall House Reviews</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>House Listing</th>
                            <th>Tenant Name</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Review Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($reviewsResult) === 0): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No reviews found in database.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($row = mysqli_fetch_assoc($reviewsResult)): ?>
                                <tr>
                                    <td><?php echo intval($row['review_id']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['house_title'] ?? 'Deleted Listing'); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['tenant_name'] ?? 'System/Deleted Tenant'); ?></td>
                                    <td>
                                        <span class="text-warning">
                                            <?php 
                                            $rating = intval($row['rating']);
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $rating) {
                                                    echo '★';
                                                } else {
                                                    echo '☆';
                                                }
                                            }
                                            ?>
                                        </span>
                                        (<?php echo $rating; ?>/5)
                                    </td>
                                    <td><?php echo nl2br(htmlspecialchars($row['comment'])); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                            <input type="hidden" name="review_id" value="<?php echo intval($row['review_id']); ?>">
                                            <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
