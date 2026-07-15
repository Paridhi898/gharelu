<?php
require_once __DIR__ . '/config.php';
require_role('landlord');

$user = current_user();
$fullName = $user['full_name'] ?? $_SESSION['username'];
$avatarLetter = strtoupper(substr($fullName, 0, 1));
$landlordId = intval($_SESSION['user_id']);

$propertyErrors = [];
$propertySuccess = '';

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_property') {
    $title = trim($_POST['title'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $houseType = trim($_POST['house_type'] ?? 'Other');
    $price = trim($_POST['price'] ?? '');
    $bedrooms = intval($_POST['bedrooms'] ?? 0);
    $bathrooms = intval($_POST['bathrooms'] ?? 0);
    $amenities = trim($_POST['amenities'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '' || $address === '' || $city === '' || $price === '' || !is_numeric($price)) {
        $propertyErrors[] = 'Please provide a valid title, address, city, and rent amount.';
    }

    if (empty($propertyErrors)) {
        $stmt = mysqli_prepare(
            $conn,
            'INSERT INTO house (landlord_id, title, description, house_type, address, city, price, bedrooms, bathrooms, amenities, availability_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)' 
        );

        if ($stmt) {
            $status = 'available';
            mysqli_stmt_bind_param($stmt, 'isssssdiiis', $landlordId, $title, $description, $houseType, $address, $city, $price, $bedrooms, $bathrooms, $amenities, $status);
            if (mysqli_stmt_execute($stmt)) {
                $propertySuccess = 'Property added successfully.';
                header('Location: landlord_dashboard.php');
                exit();
            } else {
                $propertyErrors[] = 'Unable to save property. Please try again.';
            }
        } else {
            $propertyErrors[] = 'Property form could not be processed. Please check schema setup.';
        }
    }
}

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
    'SELECT house_id, title, house_type, address, city, price, bedrooms, bathrooms, availability_status, amenities, description, created_at FROM house WHERE landlord_id = ? ORDER BY created_at DESC'
);

if ($houseStmt) {
    mysqli_stmt_bind_param($houseStmt, 'i', $landlordId);
    mysqli_stmt_execute($houseStmt);
    $houseResult = mysqli_stmt_get_result($houseStmt);

    while ($row = mysqli_fetch_assoc($houseResult)) {
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
}

$requests = [];
$requestStmt = mysqli_prepare(
    $conn,
    'SELECT ir.request_id, ir.message, ir.request_status, ir.requested_at, h.title AS house_title, u.full_name AS tenant_name, u.phone_number AS tenant_phone
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

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Landlord Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Manrope:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboard.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="logo"><span>🏠Gharelu</span></div>

  <div class="user-info">
    <div class="av"><?php echo htmlspecialchars($avatarLetter); ?></div>
    <div>
      <div class="name"><?php echo htmlspecialchars($fullName); ?></div>
      <div class="role">Landlord</div>
    </div>
  </div>

  <nav>
    <button class="nav-link active" onclick="switchTab('overview', this)">📊 Overview</button>
    <button class="nav-link" onclick="switchTab('listings', this)">🏘️ My Listings <span class="n-badge"><?php echo $listingStats['total']; ?></span></button>
    <button class="nav-link" onclick="switchTab('add', this)">➕ Add Property</button>
    <button class="nav-link" onclick="switchTab('requests', this)">📩 Requests <span class="n-badge"><?php echo $listingStats['pending_requests']; ?></span></button>
  </nav>

  <div class="sidebar-bottom">
    <a class="nav-link" href="logout.php">🚪 Logout</a>
  </div>
</aside>

<!-- MAIN -->
<div class="main">
  <header class="topbar">
    <h2 id="page-title">Overview</h2>
    <div class="topbar-right">
      <span class="notif">🔔 <?php echo $listingStats['pending_requests']; ?> pending requests</span>
    </div>
  </header>

  <div class="content">

    <!-- OVERVIEW -->
    <div class="tab-pane active" id="tab-overview">
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

    <!-- LISTINGS -->
    <div class="tab-pane" id="tab-listings">
      <div class="sh">
        <h3>My Listings</h3>
        <button class="btn btn-primary" onclick="switchTab('add', document.querySelector('[onclick*=add]'))">➕ Add Property</button>
      </div>

      <div class="filters">
        <button class="fbtn active" onclick="filterListings('all', this)">All (<?php echo $listingStats['total']; ?>)</button>
        <button class="fbtn" onclick="filterListings('available', this)">Available (<?php echo $listingStats['available']; ?>)</button>
        <button class="fbtn" onclick="filterListings('pending', this)">Pending (<?php echo $listingStats['pending']; ?>)</button>
        <button class="fbtn" onclick="filterListings('rented', this)">Rented (<?php echo $listingStats['rented']; ?>)</button>
      </div>

      <div class="listings-grid">
        <?php foreach ($houses as $listing): ?>
          <?php $badgeClass = 'badge-' . $listing['availability_status']; ?>
          <?php $statusLabel = $listing['availability_status'] === 'available' ? 'Available' : ($listing['availability_status'] === 'rented' ? 'Rented' : 'Pending'); ?>
          <div class="prop-card" data-status="<?php echo htmlspecialchars($listing['availability_status']); ?>">
            <div class="prop-img"><span class="badge <?php echo $badgeClass; ?>"><?php echo $statusLabel; ?></span></div>
            <div class="prop-body">
              <div class="prop-title"><?php echo htmlspecialchars($listing['title']); ?></div>
              <div class="prop-loc">📍 <?php echo htmlspecialchars($listing['city']); ?></div>
              <div class="prop-meta"><span>🛏 <?php echo htmlspecialchars($listing['bedrooms']); ?> Bed</span><span>🚿 <?php echo htmlspecialchars($listing['bathrooms']); ?> Bath</span></div>
              <div class="prop-price">Rs <?php echo number_format($listing['price']); ?> <small>/ month</small></div>
              <div class="prop-footer">
                <select>
                  <option<?php echo $listing['availability_status'] === 'available' ? ' selected' : ''; ?>>🟢 Available</option>
                  <option<?php echo $listing['availability_status'] === 'pending' ? ' selected' : ''; ?>>🟡 Pending</option>
                  <option<?php echo $listing['availability_status'] === 'rented' ? ' selected' : ''; ?>>🔵 Rented</option>
                </select>
                <div class="prop-actions">
                  <button class="btn btn-ghost" style="padding:5px 10px;font-size:11px">✏️</button>
                  <button class="btn btn-danger" style="padding:5px 10px;font-size:11px">🗑</button>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- ADD PROPERTY -->
    <div class="tab-pane" id="tab-add">
      <div class="sh"><h3>Add New Property</h3></div>
      <div class="form-card">
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
        <form method="POST">
          <input type="hidden" name="action" value="create_property">
          <div class="form-grid">
            <div class="fg full"><label>Title *</label><input type="text" name="title" placeholder="e.g. 2BHK Apartment in Lalitpur" required></div>
            <div class="fg"><label>Location *</label><input type="text" name="city" placeholder="e.g. Kathmandu" required></div>
            <div class="fg"><label>Type</label>
              <select name="house_type"><option>Apartment</option><option>House</option><option>Room</option><option>Flat</option><option>Studio</option></select>
            </div>
            <div class="fg"><label>Monthly Rent (Rs) *</label><input type="number" name="price" placeholder="25000" required></div>
            <div class="fg"><label>Address *</label><input type="text" name="address" placeholder="e.g. Budhanilkantha" required></div>
            <div class="fg"><label>Bedrooms</label><input type="number" name="bedrooms" placeholder="2" min="0"></div>
            <div class="fg"><label>Bathrooms</label><input type="number" name="bathrooms" placeholder="1" min="0"></div>
            <div class="fg full"><label>Amenities</label><input type="text" name="amenities" placeholder="WiFi, Parking, Water, Generator"></div>
            <div class="fg full"><label>Description</label><textarea name="description" placeholder="Describe the property..."></textarea></div>
          </div>
          <br>
          <button class="btn btn-primary">🏠 Publish Listing</button>
        </form>
      </div>
    </div>

    <!-- REQUESTS -->
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
                <?php if (!empty($request['tenant_phone'])): ?>
                  <a href="tel:<?php echo htmlspecialchars($request['tenant_phone']); ?>">📞 <?php echo htmlspecialchars($request['tenant_phone']); ?></a>
                <?php endif; ?>
              </div>
              <div class="req-msg"><?php echo htmlspecialchars($request['message']); ?></div>
              <div class="req-date">🕐 <?php echo htmlspecialchars($request['requested_at']); ?></div>
            </div>
            <div class="req-actions">
              <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($request['request_status']); ?></span>
              <?php if ($request['request_status'] === 'pending'): ?>
                <button class="btn btn-success">✅ Accept</button>
                <button class="btn btn-danger">❌ Reject</button>
              <?php elseif ($request['request_status'] === 'accepted'): ?>
                <button class="btn btn-danger">❌ Reject</button>
                <button class="btn btn-ghost">↩ Reset</button>
              <?php else: ?>
                <button class="btn btn-ghost">↩ Reset</button>
              <?php endif; ?>
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