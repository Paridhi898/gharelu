<?php
// =====================================================================
// LANDLORD DASHBOARD
// This page shows a logged-in landlord their overview stats, listings,
// an add/edit property form, and tenant interest requests.
// =====================================================================

require_once __DIR__ . '/config.php';

// Use manual check so we can diagnose unexpected redirects.
// (require_login() ensures a session exists / user is authenticated)
require_login();

// Get the role of the currently logged-in user from the session
$currentRole = $_SESSION['user_type'] ?? '';

// ---------------------------------------------------------------------
// ROLE GUARD: only landlords may view this page.
// If the session role isn't 'landlord', block access and show a
// friendly "Access Denied" page with a link to the correct dashboard.
// ---------------------------------------------------------------------
if ($currentRole !== 'landlord') {
  http_response_code(403);
  // Decide where to send the user based on their actual role
  $target = $currentRole === 'admin' ? 'admin_dashboard.php' : ($currentRole === 'tenant' ? 'tenant_dashboard.php' : 'login.php');
  // Inline HTML explaining the mismatch, plus a debug dump of session data
  echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Access Denied</title><link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Manrope:wght@400;500;600&display=swap" rel="stylesheet"><style>body{font-family:"Manrope",Arial,Helvetica,sans-serif;background:#f7fafc;color:#2d3748;display:flex;align-items:center;justify-content:center;height:100vh;margin:0} .card{background:#fff;padding:22px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,.08);max-width:720px;width:100%} a{color:#2c5f8a;text-decoration:none;margin-right:12px;font-weight:700}</style></head><body><div class="card"><h2>Access blocked — role mismatch</h2><p>Your current session role is <strong>' . htmlspecialchars($currentRole) . "'</strong>. The Landlord Dashboard requires the 'landlord' role.</p><p>If you expected to be a landlord, please <a href=\"logout.php\">log out</a> and sign in with the correct landlord account.</p><p>You can go to your dashboard: <a href=\"" . $target . "\">Continue</a></p><pre style=\"background:#f1f5f9;padding:10px;border-radius:8px;margin-top:12px;overflow:auto;\">Session data:\n" . htmlspecialchars(print_r($_SESSION, true)) . "</pre></div></body></html>";
  exit();
}

// ---------------------------------------------------------------------
// Load current user info for display in the sidebar (name, avatar, etc.)
// ---------------------------------------------------------------------
$user = current_user();
$fullName = $user['full_name'] ?? $_SESSION['username'];
// Use the first letter of the name as an avatar placeholder
$avatarLetter = strtoupper(substr($fullName, 0, 1));
$landlordId = intval($_SESSION['user_id']);

// Variables used to hold form validation errors / success messages
// and to track whether we're currently in "edit property" mode.
$propertyErrors = [];
$propertySuccess = '';
$editingHouse = null;
$isEditing = false;

// Open a database connection for this request
$conn = db_connect();

// =====================================================================
// POST HANDLER: request_action
// Handles a landlord approving/rejecting/resetting a tenant's interest
// request. Uses a JOIN to make sure the request belongs to a house
// owned by this landlord (prevents tampering with other landlords' data).
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'request_action') {
    $requestId = intval($_POST['request_id'] ?? 0);
    $requestAction = trim($_POST['request_action'] ?? '');
    // Map the button action name to the actual status value stored in DB
    $allowed = ['approve' => 'accepted', 'reject' => 'rejected', 'reset' => 'pending'];

    if ($requestId > 0 && isset($allowed[$requestAction])) {
        $status = $allowed[$requestAction];
        // Update only if the request's house belongs to this landlord
        $stmt = mysqli_prepare($conn, 'UPDATE interest_request ir JOIN house h ON ir.house_id = h.house_id SET ir.request_status = ? WHERE ir.request_id = ? AND h.landlord_id = ?');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sii', $status, $requestId, $landlordId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // Redirect back to the dashboard (Post/Redirect/Get pattern to avoid
    // duplicate form resubmission on refresh)
    header('Location: landlord_dashboard.php');
    exit();
}

// =====================================================================
// POST HANDLER: create_property
// Handles submission of the "Add New Property" form, including
// validation, optional image uploads, and inserting into the DB.
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_property') {
    // Collect and trim form fields
    $title = trim($_POST['title'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $houseType = trim($_POST['house_type'] ?? 'Other');
    $price = trim($_POST['price'] ?? '');
    $bedrooms = intval($_POST['bedrooms'] ?? 0);
    $bathrooms = intval($_POST['bathrooms'] ?? 0);
    $amenities = trim($_POST['amenities'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Basic required-field / type validation
    if ($title === '' || $address === '' || $city === '' || $price === '' || !is_numeric($price)) {
        $propertyErrors[] = 'Please provide a valid title, address, city, and rent amount.';
    }

    if (empty($propertyErrors)) {
        // Ensure the uploads directory exists
        $uploadDir = __DIR__ . '/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // -------------------------------------------------------------
        // Handle multiple uploaded images (if any). Each file is
        // validated as a real image via getimagesize() before being
        // moved to the uploads folder with a randomized safe filename.
        // -------------------------------------------------------------
        $uploadedImages = [];
        if (!empty($_FILES['images']['name']) && is_array($_FILES['images']['name'])) {
            foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
                if (empty($_FILES['images']['name'][$index]) || !is_uploaded_file($tmpName)) {
                    continue; // skip empty/invalid upload slots
                }

                $originalName = basename($_FILES['images']['name'][$index]);
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                // Generate a unique, safe filename to avoid collisions/overwrites
                $safeName = time() . '_' . uniqid('', true) . ($extension !== '' ? '.' . strtolower($extension) : '');
                $destination = $uploadDir . '/' . $safeName;

                // getimagesize() acts as a lightweight check that the file is really an image
                if (getimagesize($tmpName) !== false && move_uploaded_file($tmpName, $destination)) {
                    $uploadedImages[] = 'uploads/' . $safeName;
                }
            }
        }

        // -------------------------------------------------------------
        // Insert the new house record, then insert any uploaded images.
        // Wrapped in a transaction so a failure in image insertion rolls
        // back the whole operation (house + images stay consistent).
        // -------------------------------------------------------------
        mysqli_begin_transaction($conn);
        $stmt = mysqli_prepare(
            $conn,
            'INSERT INTO house (landlord_id, title, description, house_type, address, city, price, bedrooms, bathrooms, amenities, availability_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)' 
        );

        if ($stmt) {
            $status = 'available'; // new listings default to "available"
            mysqli_stmt_bind_param($stmt, 'isssssdiiss', $landlordId, $title, $description, $houseType, $address, $city, $price, $bedrooms, $bathrooms, $amenities, $status);
            if (mysqli_stmt_execute($stmt)) {
                $houseId = mysqli_insert_id($conn);
                $imageInsertError = false;

                // Insert each uploaded image path, linked to the new house_id
                if (!empty($uploadedImages)) {
                    $imageStmt = mysqli_prepare($conn, 'INSERT INTO house_image (house_id, image_url) VALUES (?, ?)');
                    if ($imageStmt) {
                        foreach ($uploadedImages as $imagePath) {
                            mysqli_stmt_bind_param($imageStmt, 'is', $houseId, $imagePath);
                            if (!mysqli_stmt_execute($imageStmt)) {
                                $imageInsertError = true;
                                break; // stop on first failure
                            }
                        }
                        mysqli_stmt_close($imageStmt);
                    } else {
                        $imageInsertError = true;
                    }
                }

                if ($imageInsertError) {
                    // Something went wrong saving images -> undo the house insert too
                    mysqli_rollback($conn);
                    $propertyErrors[] = 'Unable to save property images. Please try again.';
                } else {
                    // Everything succeeded -> commit and redirect (Post/Redirect/Get)
                    mysqli_commit($conn);
                    $propertySuccess = 'Property added successfully.';
                    header('Location: landlord_dashboard.php');
                    exit();
                }
            } else {
                mysqli_rollback($conn);
                $propertyErrors[] = 'Unable to save property. Please try again.';
            }
            mysqli_stmt_close($stmt);
        } else {
            // mysqli_prepare() failed - likely a schema/SQL problem
            mysqli_rollback($conn);
            $propertyErrors[] = 'Property form could not be processed. Please check schema setup.';
        }
    }
}

// =====================================================================
// POST HANDLER: update_property
// Handles editing an existing listing: updates its fields, optionally
// deletes selected existing images, and optionally adds new images.
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_property') {
    // Collect and trim form fields
    $houseId = intval($_POST['house_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $houseType = trim($_POST['house_type'] ?? 'Other');
    $price = trim($_POST['price'] ?? '');
    $bedrooms = intval($_POST['bedrooms'] ?? 0);
    $bathrooms = intval($_POST['bathrooms'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $amenities = trim($_POST['amenities'] ?? '');
    $availabilityStatus = trim($_POST['availability_status'] ?? 'available');

    // Basic required-field / type validation
    if ($houseId <= 0 || $title === '' || $address === '' || $city === '' || $price === '' || !is_numeric($price)) {
        $propertyErrors[] = 'Please provide a valid listing and make sure all required fields are filled.';
    }

    // Whitelist the availability status to only allowed values
    if (!in_array($availabilityStatus, ['available', 'pending', 'rented'], true)) {
        $availabilityStatus = 'available';
    }

    if (empty($propertyErrors)) {
        // Wrap the update + image changes in a transaction for consistency
        mysqli_begin_transaction($conn);
        $updateStmt = mysqli_prepare(
            $conn,
            'UPDATE house SET title = ?, description = ?, house_type = ?, address = ?, city = ?, price = ?, bedrooms = ?, bathrooms = ?, amenities = ?, availability_status = ? WHERE house_id = ? AND landlord_id = ?'
        );

        if ($updateStmt) {
            // Note: landlord_id is included in WHERE to ensure landlords
            // can only update their own listings
            mysqli_stmt_bind_param($updateStmt, 'sssssdiissii', $title, $description, $houseType, $address, $city, $price, $bedrooms, $bathrooms, $amenities, $availabilityStatus, $houseId, $landlordId);
            if (mysqli_stmt_execute($updateStmt)) {
                $imageManageError = false;

                // -----------------------------------------------------
                // Delete any images the landlord checked for removal.
                // The DELETE query joins back to `house` to double-check
                // ownership (landlord_id) before deleting.
                // -----------------------------------------------------
                if (!empty($_POST['delete_images']) && is_array($_POST['delete_images'])) {
                    $deleteIds = array_filter(array_map('intval', $_POST['delete_images']));
                    if (!empty($deleteIds)) {
                        $deleteList = implode(',', $deleteIds);
                        $deleteSql = "DELETE hi FROM house_image hi JOIN house h ON hi.house_id = h.house_id WHERE hi.image_id IN ($deleteList) AND hi.house_id = ? AND h.landlord_id = ?";
                        $deleteStmt = mysqli_prepare($conn, $deleteSql);
                        if ($deleteStmt) {
                            mysqli_stmt_bind_param($deleteStmt, 'ii', $houseId, $landlordId);
                            if (!mysqli_stmt_execute($deleteStmt)) {
                                $imageManageError = true;
                            }
                            mysqli_stmt_close($deleteStmt);
                        } else {
                            $imageManageError = true;
                        }
                    }
                }

                // -----------------------------------------------------
                // Upload and insert any newly added images (same logic
                // as in create_property above).
                // -----------------------------------------------------
                if (!$imageManageError && !empty($_FILES['images']['name']) && is_array($_FILES['images']['name'])) {
                    $uploadDir = __DIR__ . '/uploads';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $newImageStmt = mysqli_prepare($conn, 'INSERT INTO house_image (house_id, image_url) VALUES (?, ?)');
                    if ($newImageStmt) {
                        foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
                            if (empty($_FILES['images']['name'][$index]) || !is_uploaded_file($tmpName)) {
                                continue;
                            }
                            $originalName = basename($_FILES['images']['name'][$index]);
                            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                            $safeName = time() . '_' . uniqid('', true) . ($extension !== '' ? '.' . strtolower($extension) : '');
                            $destination = $uploadDir . '/' . $safeName;
                            if (getimagesize($tmpName) !== false && move_uploaded_file($tmpName, $destination)) {
                                $imageUrl = 'uploads/' . $safeName;
                                mysqli_stmt_bind_param($newImageStmt, 'is', $houseId, $imageUrl);
                                if (!mysqli_stmt_execute($newImageStmt)) {
                                    $imageManageError = true;
                                    break;
                                }
                            }
                        }
                        mysqli_stmt_close($newImageStmt);
                    } else {
                        $imageManageError = true;
                    }
                }

                if (!$imageManageError) {
                    // All good -> commit and redirect (Post/Redirect/Get)
                    mysqli_commit($conn);
                    mysqli_stmt_close($updateStmt);
                    header('Location: landlord_dashboard.php');
                    exit();
                }

                // Something failed while managing images -> roll back everything
                mysqli_rollback($conn);
                $propertyErrors[] = 'Unable to update property images. Please try again.';
            } else {
                mysqli_rollback($conn);
                $propertyErrors[] = 'Unable to update property. Please try again.';
            }
            mysqli_stmt_close($updateStmt);
        } else {
            mysqli_rollback($conn);
            $propertyErrors[] = 'Property update form could not be processed. Please check schema setup.';
        }
    }

    // Re-populate the edit form with the submitted values (so the user
    // doesn't lose their input if there was a validation error), and
    // flag that we're in "editing" mode so the Add/Edit tab is shown.
    $editingHouse = [
        'house_id' => $houseId,
        'title' => $title,
        'address' => $address,
        'city' => $city,
        'house_type' => $houseType,
        'price' => $price,
        'bedrooms' => $bedrooms,
        'bathrooms' => $bathrooms,
        'description' => $description,
        'amenities' => $amenities,
        'availability_status' => $availabilityStatus,
    ];
    $isEditing = true;
}

// =====================================================================
// GET HANDLER: ?edit_house_id=...
// If the landlord clicked "Edit" on a listing (and we're not already
// mid-way through processing an update_property POST above), load that
// listing's data (and its images) so the edit form can be pre-filled.
// =====================================================================
if (!$isEditing && isset($_GET['edit_house_id'])) {
    $editHouseId = intval($_GET['edit_house_id']);
    if ($editHouseId > 0) {
        // Ownership check: house_id AND landlord_id must match
        $editStmt = mysqli_prepare(
            $conn,
            'SELECT house_id, title, description, house_type, address, city, price, bedrooms, bathrooms, amenities, availability_status FROM house WHERE house_id = ? AND landlord_id = ? LIMIT 1'
        );
        if ($editStmt) {
            mysqli_stmt_bind_param($editStmt, 'ii', $editHouseId, $landlordId);
            mysqli_stmt_execute($editStmt);
            $editResult = mysqli_stmt_get_result($editStmt);
            if ($editHouse = mysqli_fetch_assoc($editResult)) {
                $editingHouse = $editHouse;
                $editingHouse['images'] = [];
                // Load all images belonging to this house for the edit form
                $imageLoadStmt = mysqli_prepare($conn, 'SELECT image_id, image_url FROM house_image WHERE house_id = ? ORDER BY image_id ASC');
                if ($imageLoadStmt) {
                    mysqli_stmt_bind_param($imageLoadStmt, 'i', $editHouseId);
                    mysqli_stmt_execute($imageLoadStmt);
                    $imageResult = mysqli_stmt_get_result($imageLoadStmt);
                    while ($imageRow = mysqli_fetch_assoc($imageResult)) {
                        $editingHouse['images'][] = $imageRow;
                    }
                    mysqli_stmt_close($imageLoadStmt);
                }
                $isEditing = true;
            } else {
                // No matching house for this landlord -> show an error instead
                $propertyErrors[] = 'Listing not found or you do not have permission to edit it.';
            }
            mysqli_stmt_close($editStmt);
        }
    }
}

// Safety net: if we're in edit mode but somehow don't have the images
// loaded yet (e.g. came from the update_property POST branch above,
// which doesn't fetch images), fetch them now so the edit form can
// show existing images with delete checkboxes.
if ($isEditing && !isset($editingHouse['images']) && !empty($editingHouse['house_id'])) {
    $editingHouse['images'] = [];
    $imageLoadStmt = mysqli_prepare($conn, 'SELECT image_id, image_url FROM house_image WHERE house_id = ? ORDER BY image_id ASC');
    if ($imageLoadStmt) {
        mysqli_stmt_bind_param($imageLoadStmt, 'i', $editingHouse['house_id']);
        mysqli_stmt_execute($imageLoadStmt);
        $imageResult = mysqli_stmt_get_result($imageLoadStmt);
        while ($imageRow = mysqli_fetch_assoc($imageResult)) {
            $editingHouse['images'][] = $imageRow;
        }
        mysqli_stmt_close($imageLoadStmt);
    }
}

// =====================================================================
// LOAD DATA FOR THE PAGE: all of this landlord's houses + summary stats
// =====================================================================
$houses = [];
$listingStats = [
    'total' => 0,
    'available' => 0,
    'pending' => 0,
    'rented' => 0,
    'pending_requests' => 0,
];

$houseStmt = mysqli_prepare(
    $conn,
    'SELECT house_id, title, house_type, address, city, price, bedrooms, bathrooms, availability_status, description, created_at FROM house WHERE landlord_id = ? ORDER BY created_at DESC'
);

if ($houseStmt) {
    mysqli_stmt_bind_param($houseStmt, 'i', $landlordId);
    mysqli_stmt_execute($houseStmt);
    $houseResult = mysqli_stmt_get_result($houseStmt);

    // Fetch each house and tally up the stats used in the Overview tab
    while ($row = mysqli_fetch_assoc($houseResult)) {
        $row['first_image'] = ''; // placeholder, filled in below
        $houses[] = $row;
        $listingStats['total']++;
        if ($row['availability_status'] === 'available') {
            $listingStats['available']++;
        } elseif ($row['availability_status'] === 'rented') {
            $listingStats['rented']++;
        } else {
            $listingStats['pending']++;
        }
    }

    // For each house, look up its first image (used as the card thumbnail).
    // Falls back to a generated "No Image Available" placeholder SVG.
    foreach ($houses as &$house) {
        $imageStmt = mysqli_prepare($conn, 'SELECT image_id, image_url FROM house_image WHERE house_id = ? ORDER BY image_id ASC LIMIT 1');
        if ($imageStmt) {
            mysqli_stmt_bind_param($imageStmt, 'i', $house['house_id']);
            mysqli_stmt_execute($imageStmt);
            $imageResult = mysqli_stmt_get_result($imageStmt);
            if ($imageRow = mysqli_fetch_assoc($imageResult)) {
                $house['first_image'] = $imageRow['image_url'] ?? '';
            }
            mysqli_stmt_close($imageStmt);
        }
        if (empty($house['first_image'])) {
            // Inline data-URI SVG placeholder when no image exists
            $house['first_image'] = 'data:image/svg+xml;charset=UTF-8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="400" height="240"><rect width="400" height="240" fill="#f3f4f6"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#9ca3af" font-family="Arial, Helvetica, sans-serif" font-size="20">No Image Available</text></svg>');
        }
    }
    unset($house); // break the reference from the foreach loop above
}

// =====================================================================
// LOAD DATA FOR THE PAGE: tenant interest requests for this landlord's
// houses, including tenant contact info.
// =====================================================================
$requests = [];
$requestStmt = mysqli_prepare(
    $conn,
    'SELECT ir.request_id, ir.message, ir.request_status, ir.requested_at, h.title AS house_title, u.full_name AS tenant_name, u.email AS tenant_email, u.phone_number AS tenant_phone
     FROM interest_request ir
     JOIN house h ON ir.house_id = h.house_id
     JOIN users u ON ir.tenant_id = u.id
     WHERE h.landlord_id = ?
     ORDER BY ir.requested_at DESC'
);

if ($requestStmt) {
    mysqli_stmt_bind_param($requestStmt, 'i', $landlordId);
    mysqli_stmt_execute($requestStmt);
    $requestResult = mysqli_stmt_get_result($requestStmt);

    while ($row = mysqli_fetch_assoc($requestResult)) {
        $requests[] = $row;
        if ($row['request_status'] === 'pending') {
            $listingStats['pending_requests']++;
        }
    }
}

// Done with the database for this request
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Landlord Dashboard</title>
<link rel="stylesheet" href="dashboard.css">
</head>
<body>

<!-- SIDEBAR: branding, logged-in user info, and tab navigation -->
<aside class="sidebar">

  <!-- Avatar + name now doubles as the Profile link -->
  <a class="user-info" href="profile.php" title="View Profile">
    <div class="av"><?php echo htmlspecialchars($avatarLetter); ?></div>
    <div>
      <div class="name"><?php echo htmlspecialchars($fullName); ?></div>
      <div class="role">Landlord</div>
    </div>
  </a>

  <nav>
    <!-- Each nav button calls switchTab() (in script.js) to show/hide tab panes -->
    <button class="nav-link<?php echo $isEditing ? '' : ' active'; ?>" onclick="switchTab('overview', this)">📊 Overview</button>
    <button class="nav-link" onclick="switchTab('listings', this)">🏘️ My Listings <span class="n-badge"><?php echo $listingStats['total']; ?></span></button>
    <!-- "Add Property" tab is auto-activated when we're in edit mode -->
    <button class="nav-link<?php echo $isEditing ? ' active' : ''; ?>" onclick="switchTab('add', this)">➕ Add Property</button>
    <button class="nav-link" onclick="switchTab('requests', this)">📩 Requests <span class="n-badge"><?php echo $listingStats['pending_requests']; ?></span></button>
  </nav>

  <div class="sidebar-bottom">
    <a class="nav-link" href="logout.php">🚪 Logout</a>
  </div>
</aside>

<!-- MAIN CONTENT AREA -->
<div class="main">
  <header class="topbar">
    <h2 id="page-title"><?php echo $isEditing ? 'Edit Property' : 'Overview'; ?></h2>
    <div class="topbar-right">
      <span class="notif">🔔 <?php echo $listingStats['pending_requests']; ?> pending requests</span>
    </div>
  </header>

  <div class="content">

    <!-- ================= OVERVIEW TAB ================= -->
    <div class="tab-pane<?php echo $isEditing ? '' : ' active'; ?>" id="tab-overview">
      <!-- Summary stat cards -->
      <div class="stats">
        <div class="stat">
          <div class="stat-icon ic-o">🏘️</div>
          <div><div class="stat-num"><?php echo $listingStats['total']; ?></div><div class="stat-label">Total Listings</div></div>
        </div>
        <div class="stat">
          <div class="stat-icon ic-g">✅</div>
          <div><div class="stat-num"><?php echo $listingStats['available']; ?></div><div class="stat-label">Available</div></div>
        </div>
        <div class="stat">
          <div class="stat-icon ic-b">🔑</div>
          <div><div class="stat-num"><?php echo $listingStats['rented']; ?></div><div class="stat-label">Rented</div></div>
        </div>
        <div class="stat">
          <div class="stat-icon ic-r">📩</div>
          <div><div class="stat-num"><?php echo $listingStats['pending_requests']; ?></div><div class="stat-label">Pending Requests</div></div>
        </div>
      </div>

      <div class="two-col">
        <!-- Preview of the 3 most recent listings -->
        <div class="panel">
          <h4>Recent Listings</h4>
          <?php foreach (array_slice($houses, 0, 3) as $house): ?>
            <?php $statusLabel = $house['availability_status'] === 'available' ? 'Available' : ($house['availability_status'] === 'rented' ? 'Rented' : 'Pending'); ?>
            <div class="mini-row">
              <div>
                <div class="mr-name"><?php echo htmlspecialchars($house['title']); ?></div>
                <div class="mr-sub">📍 <?php echo htmlspecialchars($house['city']); ?></div>
              </div>
              <span class="badge badge-<?php echo htmlspecialchars($house['availability_status']); ?>"><?php echo $statusLabel; ?></span>
            </div>
          <?php endforeach; ?>
        </div>
        <!-- Preview of the 3 most recent tenant requests -->
        <div class="panel">
          <h4>Recent Requests</h4>
          <?php foreach (array_slice($requests, 0, 3) as $request): ?>
            <div class="mini-row">
              <div>
                <div class="mr-name"><?php echo htmlspecialchars($request['tenant_name']); ?></div>
                <div class="mr-sub"><?php echo htmlspecialchars($request['house_title']); ?></div>
              </div>
              <span class="badge badge-<?php echo htmlspecialchars($request['request_status']); ?>"><?php echo ucfirst($request['request_status']); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- ================= LISTINGS TAB ================= -->
    <div class="tab-pane" id="tab-listings">
      <div class="sh">
        <h3>My Listings</h3>
        <button class="btn btn-primary" onclick="switchTab('add', document.querySelector('[onclick*=add]'))">➕ Add Property</button>
      </div>

      <!-- Client-side filter buttons (filtering handled in script.js) -->
      <div class="filters">
        <button class="fbtn active" onclick="filterListings('all', this)">All (<?php echo $listingStats['total']; ?>)</button>
        <button class="fbtn" onclick="filterListings('available', this)">Available (<?php echo $listingStats['available']; ?>)</button>
        <button class="fbtn" onclick="filterListings('pending', this)">Pending (<?php echo $listingStats['pending']; ?>)</button>
        <button class="fbtn" onclick="filterListings('rented', this)">Rented (<?php echo $listingStats['rented']; ?>)</button>
      </div>

      <!-- Grid of property cards, one per listing -->
      <div class="listings-grid">
        <?php foreach ($houses as $listing): ?>
          <?php $badgeClass = 'badge-' . $listing['availability_status']; ?>
          <?php $statusLabel = $listing['availability_status'] === 'available' ? 'Available' : ($listing['availability_status'] === 'rented' ? 'Rented' : 'Pending'); ?>
          <div class="prop-card" data-status="<?php echo htmlspecialchars($listing['availability_status']); ?>">
            <div class="prop-img">
              <?php if (!empty($listing['first_image'])): ?>
                <img src="<?php echo htmlspecialchars($listing['first_image']); ?>" alt="<?php echo htmlspecialchars($listing['title']); ?>" style="width:100%;height:100%;object-fit:cover;">
              <?php endif; ?>
              <span class="badge <?php echo $badgeClass; ?>"><?php echo $statusLabel; ?></span>
            </div>
            <div class="prop-body">
              <div class="prop-title"><?php echo htmlspecialchars($listing['title']); ?></div>
              <div class="prop-loc">📍 <?php echo htmlspecialchars($listing['city']); ?></div>
              <div class="prop-meta"><span>🛏 <?php echo htmlspecialchars($listing['bedrooms']); ?> Bed</span><span>🚿 <?php echo htmlspecialchars($listing['bathrooms']); ?> Bath</span></div>
              <div class="prop-price">Rs <?php echo number_format($listing['price']); ?> <small>/ month</small></div>
              <div class="prop-footer">
                <!-- Note: this status dropdown is currently display-only;
                     changing status is done via the Edit form below -->
                <select>
                  <option<?php echo $listing['availability_status'] === 'available' ? ' selected' : ''; ?>>🟢 Available</option>
                  <option<?php echo $listing['availability_status'] === 'pending' ? ' selected' : ''; ?>>🟡 Pending</option>
                  <option<?php echo $listing['availability_status'] === 'rented' ? ' selected' : ''; ?>>🔵 Rented</option>
                </select>
                <div class="prop-actions">
                  <a class="btn btn-ghost" style="padding:5px 10px" href="landlord_dashboard.php?edit_house_id=<?php echo $listing['house_id']; ?>">✏️ Edit</a>
                  <button class="btn btn-danger" style="padding:5px 10px">🗑</button>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- ================= ADD / EDIT PROPERTY TAB ================= -->
    <div class="tab-pane<?php echo $isEditing ? ' active' : ''; ?>" id="tab-add">
      <div class="sh"><h3><?php echo $isEditing ? 'Edit Property' : 'Add New Property'; ?></h3></div>
      <div class="form-card">
        <!-- Show validation errors or a success message above the form -->
        <?php if (!empty($propertyErrors)): ?>
          <div class="error-message">
            <ul>
              <?php foreach ($propertyErrors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php elseif ($propertySuccess): ?>
          <div class="success-message"><?php echo htmlspecialchars($propertySuccess); ?></div>
        <?php endif; ?>
        <!-- Single form used for both "create" and "update", switched via hidden "action" field -->
        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="<?php echo $isEditing ? 'update_property' : 'create_property'; ?>">
          <?php if ($isEditing): ?>
            <input type="hidden" name="house_id" value="<?php echo htmlspecialchars($editingHouse['house_id']); ?>">
          <?php endif; ?>
          <div class="form-grid">
            <div class="fg full"><label>Title *</label><input type="text" name="title" placeholder="e.g. 2BHK Apartment in Lalitpur" value="<?php echo htmlspecialchars($editingHouse['title'] ?? ''); ?>" required></div>
            <div class="fg"><label>Location *</label><input type="text" name="city" placeholder="e.g. Kathmandu" value="<?php echo htmlspecialchars($editingHouse['city'] ?? ''); ?>" required></div>
            <div class="fg"><label>Type</label>
              <select name="house_type">
                <?php $typeOptions = ['Apartment','House','Room','Flat','Studio']; ?>
                <?php foreach ($typeOptions as $typeOption): ?>
                  <option<?php echo (isset($editingHouse['house_type']) && $editingHouse['house_type'] === $typeOption) ? ' selected' : ''; ?>><?php echo htmlspecialchars($typeOption); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="fg"><label>Monthly Rent (Rs) *</label><input type="number" name="price" placeholder="25000" value="<?php echo htmlspecialchars($editingHouse['price'] ?? ''); ?>" required></div>
            <div class="fg"><label>Address *</label><input type="text" name="address" placeholder="e.g. Budhanilkantha" value="<?php echo htmlspecialchars($editingHouse['address'] ?? ''); ?>" required></div>
            <div class="fg"><label>Bedrooms</label><input type="number" name="bedrooms" placeholder="2" min="0" value="<?php echo htmlspecialchars($editingHouse['bedrooms'] ?? ''); ?>"></div>
            <div class="fg"><label>Bathrooms</label><input type="number" name="bathrooms" placeholder="1" min="0" value="<?php echo htmlspecialchars($editingHouse['bathrooms'] ?? ''); ?>"></div>
            <div class="fg full"><label>Amenities</label><input type="text" name="amenities" placeholder="WiFi, Parking, Water, Generator" value="<?php echo htmlspecialchars($editingHouse['amenities'] ?? ''); ?>"></div>
            <!-- Only shown when editing and the listing already has images:
                 each image can be checked for deletion on save -->
            <?php if ($isEditing && !empty($editingHouse['images'])): ?>
              <div class="fg full"><label>Existing Images</label>
                <div>
                  <?php foreach ($editingHouse['images'] as $img): ?>
                    <div style="display:inline-block; margin-right:10px; margin-bottom:10px; text-align:center;">
                      <img src="<?php echo htmlspecialchars($img['image_url']); ?>" alt="Image" style="width:100px; height:70px; object-fit:cover; display:block; border:1px solid #ddd; margin-bottom:4px;">
                      <label style="color:#555;"><input type="checkbox" name="delete_images[]" value="<?php echo intval($img['image_id']); ?>"> Delete</label>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
            <div class="fg full"><label>Property Images</label><input type="file" name="images[]" multiple accept="image/*"></div>
            <div class="fg full"><label>Description</label><textarea name="description" placeholder="Describe the property..."><?php echo htmlspecialchars($editingHouse['description'] ?? ''); ?></textarea></div>
            <!-- Availability status is only editable on the edit form (new listings default to "available") -->
            <?php if ($isEditing): ?>
              <div class="fg full"><label>Availability Status</label>
                <select name="availability_status">
                  <option value="available"<?php echo ($editingHouse['availability_status'] ?? '') === 'available' ? ' selected' : ''; ?>>🟢 Available</option>
                  <option value="pending"<?php echo ($editingHouse['availability_status'] ?? '') === 'pending' ? ' selected' : ''; ?>>🟡 Pending</option>
                  <option value="rented"<?php echo ($editingHouse['availability_status'] ?? '') === 'rented' ? ' selected' : ''; ?>>🔵 Rented</option>
                </select>
              </div>
            <?php endif; ?>
          </div>
          <br>
          <button class="btn btn-primary"><?php echo $isEditing ? '💾 Update Listing' : '🏠 Publish Listing'; ?></button>
          <?php if ($isEditing): ?>
            <a class="btn btn-ghost" href="landlord_dashboard.php">Cancel</a>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <!-- ================= REQUESTS TAB ================= -->
    <div class="tab-pane" id="tab-requests">
      <div class="sh"><h3>Tenant Requests</h3></div>
      <div class="req-list">
        <?php foreach ($requests as $request): ?>
          <?php $badgeClass = 'badge-' . $request['request_status']; ?>
          <div class="req-card">
            <div>
              <div class="req-prop"><?php echo htmlspecialchars($request['house_title']); ?></div>
              <div class="req-name"><?php echo htmlspecialchars($request['tenant_name']); ?></div>
              <div class="req-contact">
                <a href="mailto:<?php echo htmlspecialchars($request['tenant_email']); ?>">✉️ <?php echo htmlspecialchars($request['tenant_email']); ?></a>
                <?php if (!empty($request['tenant_phone'])): ?>
                  <a href="tel:<?php echo htmlspecialchars($request['tenant_phone']); ?>">📞 <?php echo htmlspecialchars($request['tenant_phone']); ?></a>
                <?php endif; ?>
              </div>
              <div class="req-msg"><?php echo htmlspecialchars($request['message']); ?></div>
              <div class="req-date">🕐 <?php echo htmlspecialchars($request['requested_at']); ?></div>
            </div>
            <div class="req-actions">
              <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($request['request_status']); ?></span>
              <!-- Action buttons shown depend on the current request status -->
              <form method="POST" style="display:inline-block; margin-top:8px;">
                <input type="hidden" name="action" value="request_action">
                <input type="hidden" name="request_id" value="<?php echo intval($request['request_id']); ?>">
                <?php if ($request['request_status'] === 'pending'): ?>
                  <button class="btn btn-success" type="submit" name="request_action" value="approve">✅ Accept</button>
                  <button class="btn btn-danger" type="submit" name="request_action" value="reject">❌ Reject</button>
                <?php elseif ($request['request_status'] === 'accepted'): ?>
                  <button class="btn btn-danger" type="submit" name="request_action" value="reject">❌ Reject</button>
                  <button class="btn btn-ghost" type="submit" name="request_action" value="reset">↩ Reset</button>
                <?php else: ?>
                  <button class="btn btn-ghost" type="submit" name="request_action" value="reset">↩ Reset</button>
                <?php endif; ?>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</div>

<script src="script.js"></script>
</body>
</html>