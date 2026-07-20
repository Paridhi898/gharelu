<?php
require_once __DIR__ . '/config.php';

require_role('tenant');

$user = current_user();
$fullName = $user['full_name'] ?? $_SESSION['username'];
$tenantId = intval($_SESSION['user_id']);

$conn = db_connect();
$activeBookings = 0;
$pendingRequests = 0;
$savedHomesCount = 0;
$bookings = [];
$savedHomes = [];

$requestCountsStmt = mysqli_prepare($conn, 'SELECT request_status, COUNT(*) AS cnt FROM interest_request WHERE tenant_id = ? GROUP BY request_status');
if ($requestCountsStmt) {
    mysqli_stmt_bind_param($requestCountsStmt, 'i', $tenantId);
    mysqli_stmt_execute($requestCountsStmt);
    $result = mysqli_stmt_get_result($requestCountsStmt);
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['request_status'] === 'accepted') {
            $activeBookings = intval($row['cnt']);
        } elseif ($row['request_status'] === 'pending') {
            $pendingRequests = intval($row['cnt']);
        }
    }
}

$favoriteStmt = mysqli_prepare($conn, 'SELECT COUNT(*) AS cnt FROM favorite WHERE tenant_id = ?');
if ($favoriteStmt) {
    mysqli_stmt_bind_param($favoriteStmt, 'i', $tenantId);
    mysqli_stmt_execute($favoriteStmt);
    $favoriteResult = mysqli_stmt_get_result($favoriteStmt);
    $favoriteRow = mysqli_fetch_assoc($favoriteResult);
    $savedHomesCount = intval($favoriteRow['cnt'] ?? 0);
}

$bookingListStmt = mysqli_prepare(
    $conn,
    'SELECT ir.request_id, ir.request_status, ir.requested_at, h.title, h.city, h.price, h.bedrooms, h.bathrooms
     FROM interest_request ir
     JOIN house h ON ir.house_id = h.house_id
     WHERE ir.tenant_id = ?
     ORDER BY ir.requested_at DESC'
);
if ($bookingListStmt) {
    mysqli_stmt_bind_param($bookingListStmt, 'i', $tenantId);
    mysqli_stmt_execute($bookingListStmt);
    $bookingResult = mysqli_stmt_get_result($bookingListStmt);
    while ($row = mysqli_fetch_assoc($bookingResult)) {
        $bookings[] = $row;
    }
}

$savedHomesStmt = mysqli_prepare(
    $conn,
    'SELECT h.house_id, h.title, h.city, h.price, h.bedrooms, h.bathrooms
     FROM favorite f
     JOIN house h ON f.house_id = h.house_id
     WHERE f.tenant_id = ?
  ORDER BY f.favorite_id DESC'
);
if ($savedHomesStmt) {
    mysqli_stmt_bind_param($savedHomesStmt, 'i', $tenantId);
    mysqli_stmt_execute($savedHomesStmt);
    $savedResult = mysqli_stmt_get_result($savedHomesStmt);
    while ($row = mysqli_fetch_assoc($savedResult)) {
        $savedHomes[] = $row;
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<aside class="sidebar">
  <div class="logo"><span>Gharelu<em></em></span></div>

  <div class="user-info">
    <div class="av"><?php echo strtoupper(substr($fullName, 0, 1)); ?></div>
    <div>
      <div class="name"><?php echo htmlspecialchars($fullName); ?></div>
      <div class="role">Tenant</div>
    </div>
  </div>

  <nav>
    <a class="nav-link active" href="#overview">📊 Overview</a>
    <a class="nav-link" href="#bookings">🗓️ My Bookings</a>
    <a class="nav-link" href="#saved">💾 Saved Homes</a>
    <a class="nav-link" href="#profile">👤 Profile</a>
  </nav>

  <div class="sidebar-bottom">
    <a class="nav-link" href="logout.php">🚪 Logout</a>
  </div>
</aside>

<div class="main">
  <header class="topbar">
    <h2 id="page-title">Tenant Dashboard</h2>
    <div class="topbar-right">
      <span class="notif">Welcome back, <?php echo htmlspecialchars($fullName); ?></span>
    </div>
  </header>

  <div class="content">
    <section id="overview" class="tab-pane active">
      <div class="stats">
        <div class="stat">
          <div class="stat-icon ic-o">🏠</div>
          <div><div class="stat-num"><?php echo $activeBookings; ?></div><div class="stat-label">Active Bookings</div></div>
        </div>
        <div class="stat">
          <div class="stat-icon ic-g">💾</div>
          <div><div class="stat-num"><?php echo $savedHomesCount; ?></div><div class="stat-label">Saved Homes</div></div>
        </div>
        <div class="stat">
          <div class="stat-icon ic-b">📩</div>
          <div><div class="stat-num"><?php echo $pendingRequests; ?></div><div class="stat-label">Pending Requests</div></div>
        </div>
        <div class="stat">
          <div class="stat-icon ic-r">🔍</div>
          <div><div class="stat-num">0</div><div class="stat-label">Searches</div></div>
        </div>
      </div>

      <div class="panel">
        <h4>Welcome to your tenant dashboard</h4>
        <p>Use this page to manage bookings, saved properties, and your account details. Your next step is to connect the dashboard to property and booking tables in the database.</p>
      </div>
    </section>

    <section id="bookings" class="tab-pane">
      <div class="sh"><h3>My Bookings</h3></div>
      <div class="panel">
        <?php if (empty($bookings)): ?>
          <p>No bookings found yet.</p>
          <p>Browse properties and send requests to start renting.</p>
        <?php else: ?>
          <table class="table">
            <thead>
              <tr>
                <th>Property</th>
                <th>Location</th>
                <th>Status</th>
                <th>Requested</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bookings as $booking): ?>
              <tr>
                <td><?php echo htmlspecialchars($booking['title']); ?></td>
                <td><?php echo htmlspecialchars($booking['city']); ?></td>
                <td><?php echo ucfirst(htmlspecialchars($booking['request_status'])); ?></td>
                <td><?php echo htmlspecialchars($booking['requested_at']); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </section>

    <section id="saved" class="tab-pane">
      <div class="sh"><h3>Saved Homes</h3></div>
      <div class="panel">
        <?php if (empty($savedHomes)): ?>
          <p>You haven't saved any homes yet.</p>
          <p>Mark listings as favorites to keep them here.</p>
        <?php else: ?>
          <div class="saved-grid">
            <?php foreach ($savedHomes as $home): ?>
              <div class="saved-card">
                <h4><?php echo htmlspecialchars($home['title']); ?></h4>
                <p>📍 <?php echo htmlspecialchars($home['city']); ?></p>
                <p>Rs <?php echo number_format($home['price']); ?> / month</p>
                <p>🛏 <?php echo htmlspecialchars($home['bedrooms']); ?> • 🚿 <?php echo htmlspecialchars($home['bathrooms']); ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section id="profile" class="tab-pane">
      <div class="sh"><h3>Profile</h3></div>
      <div class="panel">
        <p><strong>Name:</strong> <?php echo htmlspecialchars($fullName); ?></p>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        <p><strong>Role:</strong> Tenant</p>
      </div>
    </section>
  </div>
</div>

</body>
</html>
