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
        $landlordStmt = mysqli_prepare($conn, 'UPDATE landlord SET verification_status = ? WHERE user_id = ?');
        if ($landlordStmt) {
            mysqli_stmt_bind_param($landlordStmt, 'si', $status, $id);
            mysqli_stmt_execute($landlordStmt);
        }

        $notice = $action === 'approve' ? 'Landlord verified successfully.' : 'Landlord rejected successfully.';
        header('Location: admin_dashboard.php?notice=' . urlencode($notice));
        exit();
    }
}

$totalUsers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$totalLandlords = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE user_type='landlord'"))[0];
$totalTenants = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE user_type='tenant'"))[0];

$pending = mysqli_query($conn, "SELECT u.id, u.username, u.full_name, u.email, u.phone_number, u.citizenship_id, l.verification_status FROM users u JOIN landlord l ON l.user_id = u.id WHERE u.user_type='landlord' AND l.verification_status = 'pending' ORDER BY u.id DESC");
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

    <link rel="stylesheet" href="dashboard.css">

</head>

<body>

<div class="main" style="margin-left:0;">
  <header class="topbar">
    <h2>Admin Dashboard</h2>
    <div class="topbar-right">
      <span class="notif">System Administrator</span>
      <a class="btn btn-ghost" href="logout.php">Logout</a>
    </div>
  </header>

  <div class="content">
    <div class="stats">
      <div class="stat">
        <div class="stat-icon ic-o">👥</div>
        <div><div class="stat-num"><?php echo $totalUsers; ?></div><div class="stat-label">Total Users</div></div>
      </div>
      <div class="stat">
        <div class="stat-icon ic-g">🏠</div>
        <div><div class="stat-num"><?php echo $totalLandlords; ?></div><div class="stat-label">Landlords</div></div>
      </div>
      <div class="stat">
        <div class="stat-icon ic-b">🧑</div>
        <div><div class="stat-num"><?php echo $totalTenants; ?></div><div class="stat-label">Tenants</div></div>
      </div>
      <div class="stat">
        <div class="stat-icon ic-r">⏳</div>
        <div><div class="stat-num"><?php echo $pendingCount; ?></div><div class="stat-label">Pending Verification</div></div>
      </div>
    </div>

    <div class="panel">
      <div class="sh">
        <h3>Pending Landlord Verification</h3>
      </div>

      <?php if ($notice !== ''): ?>
        <div class="notice-message"><?php echo htmlspecialchars($notice); ?></div>
      <?php endif; ?>

      <div class="verification-body">
        <?php if ($pendingCount == 0): ?>
          <p class="empty-state">No pending landlord verifications.</p>
        <?php else: ?>
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

            <?php while ($row = mysqli_fetch_assoc($pending)): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                <td><?php echo htmlspecialchars($row['citizenship_id']); ?></td>
                <td><span class="badge badge-pending">Pending</span></td>
                <td class="action-col">
                  <form method="POST" class="inline-actions">
                    <input type="hidden" name="user_id" value="<?php echo intval($row['id']); ?>">
                    <button type="submit" name="action" value="approve" class="approve-btn">Approve</button>
                    <button type="submit" name="action" value="reject" class="reject-btn">Reject</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          </table>
        <?php endif; ?>
      </div>
    </div>

      </div>
    </div>
  </div>
</div>

</body>

</html>