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
        $houseId = intval($_POST['house_id'] ?? 0);
        if ($houseId > 0) {
            // Delete related rows first due to constraints/MyISAM limitations
            mysqli_query($conn, "DELETE FROM favorite WHERE house_id = $houseId");
            mysqli_query($conn, "DELETE FROM house_amenity WHERE house_id = $houseId");
            mysqli_query($conn, "DELETE FROM house_image WHERE house_id = $houseId");
            mysqli_query($conn, "DELETE FROM interest_request WHERE house_id = $houseId");
            mysqli_query($conn, "DELETE FROM review WHERE house_id = $houseId");

            // Finally, delete the house listing
            $stmt = mysqli_prepare($conn, 'DELETE FROM house WHERE house_id = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $houseId);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $notice = 'Listing and all related records deleted successfully.';
            } else {
                $error = 'Failed to prepare listing delete statement.';
            }
        }
        header('Location: manage_listings.php?notice=' . urlencode($notice) . '&error=' . urlencode($error));
        exit();
    }

    if ($action === 'edit') {
        $houseId = intval($_POST['house_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $houseType = trim($_POST['house_type'] ?? 'Other');
        $price = floatval($_POST['price'] ?? 0.0);
        $bedrooms = intval($_POST['bedrooms'] ?? 0);
        $bathrooms = intval($_POST['bathrooms'] ?? 0);
        $status = trim($_POST['availability_status'] ?? 'available');
        $amenities = trim($_POST['amenities'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($houseId > 0 && $title !== '' && $city !== '' && $address !== '' && $price > 0) {
            $stmt = mysqli_prepare($conn, 'UPDATE house SET title = ?, city = ?, address = ?, house_type = ?, price = ?, bedrooms = ?, bathrooms = ?, availability_status = ?, amenities = ?, description = ? WHERE house_id = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssssdiisssi', $title, $city, $address, $houseType, $price, $bedrooms, $bathrooms, $status, $amenities, $description, $houseId);
                if (mysqli_stmt_execute($stmt)) {
                    $notice = 'Listing updated successfully.';
                } else {
                    $error = 'Failed to execute listing update.';
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = 'Failed to prepare listing update statement.';
            }
        } else {
            $error = 'Invalid data provided for editing listing.';
        }
        header('Location: manage_listings.php?notice=' . urlencode($notice) . '&error=' . urlencode($error));
        exit();
    }
}

// Fetch all houses with landlord names
$query = "
    SELECT h.*, u.full_name AS landlord_name 
    FROM house h
    LEFT JOIN users u ON h.landlord_id = u.id
    ORDER BY h.house_id DESC
";
$listingsResult = mysqli_query($conn, $query);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Listings - Gharelu</title>
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
                    <a class="nav-link active" href="manage_listings.php">
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

<!-- Page Header Section -->
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4"><i class="fas fa-tasks me-2"></i>Manage Listings</h2>
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

<!-- Listings Table -->
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0"><i class="fas fa-building me-2"></i>Overall House Listings</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Landlord</th>
                            <th>Type</th>
                            <th>City / Address</th>
                            <th>Rent (Rs)</th>
                            <th>Beds/Baths</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($listingsResult) === 0): ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No house listings found in database.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($row = mysqli_fetch_assoc($listingsResult)): ?>
                                <tr>
                                    <td><?php echo intval($row['house_id']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['landlord_name'] ?? 'System/Unknown'); ?></td>
                                    <td><?php echo htmlspecialchars($row['house_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['city'] . ', ' . $row['address']); ?></td>
                                    <td>Rs <?php echo number_format($row['price']); ?></td>
                                    <td><?php echo intval($row['bedrooms']) . 'B / ' . intval($row['bathrooms']) . 'T'; ?></td>
                                    <td>
                                        <?php if ($row['availability_status'] === 'available'): ?>
                                            <span class="badge bg-success">Available</span>
                                        <?php elseif ($row['availability_status'] === 'rented'): ?>
                                            <span class="badge bg-primary">Rented</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm me-1 btn-edit-listing" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal"
                                                data-id="<?php echo intval($row['house_id']); ?>"
                                                data-title="<?php echo htmlspecialchars($row['title']); ?>"
                                                data-city="<?php echo htmlspecialchars($row['city']); ?>"
                                                data-address="<?php echo htmlspecialchars($row['address']); ?>"
                                                data-type="<?php echo htmlspecialchars($row['house_type']); ?>"
                                                data-price="<?php echo floatval($row['price']); ?>"
                                                data-bedrooms="<?php echo intval($row['bedrooms']); ?>"
                                                data-bathrooms="<?php echo intval($row['bathrooms']); ?>"
                                                data-status="<?php echo htmlspecialchars($row['availability_status']); ?>"
                                                data-amenities="<?php echo htmlspecialchars($row['amenities'] ?? ''); ?>"
                                                data-description="<?php echo htmlspecialchars($row['description'] ?? ''); ?>">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this listing and all related requests, reviews, and bookings?');">
                                            <input type="hidden" name="house_id" value="<?php echo intval($row['house_id']); ?>">
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

<!-- Edit Listing Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="house_id" id="edit_house_id">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="editModalLabel"><i class="fas fa-edit me-2"></i>Edit Listing Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="edit_title" class="form-label">Title *</label>
                            <input type="text" class="form-control" name="title" id="edit_title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_city" class="form-label">City *</label>
                            <input type="text" class="form-control" name="city" id="edit_city" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_address" class="form-label">Address *</label>
                            <input type="text" class="form-control" name="address" id="edit_address" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_house_type" class="form-label">House Type</label>
                            <select class="form-select" name="house_type" id="edit_house_type">
                                <option value="Apartment">Apartment</option>
                                <option value="House">House</option>
                                <option value="Villa">Villa</option>
                                <option value="Studio">Studio</option>
                                <option value="Flat">Flat</option>
                                <option value="Room">Room</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_price" class="form-label">Monthly Rent (Rs) *</label>
                            <input type="number" class="form-control" name="price" id="edit_price" min="0" step="1" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_availability_status" class="form-label">Status</label>
                            <select class="form-select" name="availability_status" id="edit_availability_status">
                                <option value="available">Available</option>
                                <option value="pending">Pending</option>
                                <option value="rented">Rented</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_bedrooms" class="form-label">Bedrooms</label>
                            <input type="number" class="form-control" name="bedrooms" id="edit_bedrooms" min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_bathrooms" class="form-label">Bathrooms</label>
                            <input type="number" class="form-control" name="bathrooms" id="edit_bathrooms" min="0">
                        </div>
                        <div class="col-md-12">
                            <label for="edit_amenities" class="form-label">Amenities (Comma separated)</label>
                            <input type="text" class="form-control" name="amenities" id="edit_amenities" placeholder="e.g. WiFi, Parking, Garden">
                        </div>
                        <div class="col-md-12">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Footer -->
<div class="container mt-5 mb-4">
    <div class="text-center text-muted">
        <small>&copy; 2026 Gharelu House Rental System. All rights reserved.</small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Populate Modal with listing data on Edit button click
    const editButtons = document.querySelectorAll('.btn-edit-listing');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('edit_house_id').value = this.getAttribute('data-id');
            document.getElementById('edit_title').value = this.getAttribute('data-title');
            document.getElementById('edit_city').value = this.getAttribute('data-city');
            document.getElementById('edit_address').value = this.getAttribute('data-address');
            document.getElementById('edit_house_type').value = this.getAttribute('data-type');
            document.getElementById('edit_price').value = this.getAttribute('data-price');
            document.getElementById('edit_bedrooms').value = this.getAttribute('data-bedrooms');
            document.getElementById('edit_bathrooms').value = this.getAttribute('data-bathrooms');
            document.getElementById('edit_availability_status').value = this.getAttribute('data-status');
            document.getElementById('edit_amenities').value = this.getAttribute('data-amenities');
            document.getElementById('edit_description').value = this.getAttribute('data-description');
        });
    });
</script>
</body>
</html>
