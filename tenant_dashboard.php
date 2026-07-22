<?php
/* ==========================================================================
   TENANT DASHBOARD — Gharelu
   --------------------------------------------------------------------------
   Handles: browsing houses, saving/removing favorites, sending interest
   requests to landlords, and writing/deleting reviews.

=================================================================== */
require_role('tenant');

$user = current_user();
$fullName = $user['full_name'] ?? $_SESSION['username'];
$email = $user['email'] ?? '';
$tenantId = intval($_SESSION['user_id']);

require_once 'config.php';
$conn = db_connect();

/* ---------------------- AUTH CHECK ---------------------- */
if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'tenant') {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

require_once 'config.php';
$conn = db_connect();

/* ---------------------- AUTH CHECK ---------------------- */
if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'tenant') {
    header("Location: login.php");
    exit;
}
$user_id = (int) $_SESSION['user_id'];

/* ---------------------- GET TENANT ID ---------------------- */
$stmt = mysqli_prepare($conn, "SELECT tenant_id, occupation FROM tenant WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$tenant_row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$tenant_row) {
    die("No tenant profile found for this account. Please contact the admin.");
}
$tenant_id = (int) $tenant_row['tenant_id'];

/* ---------------------- GET USER NAME (for header) ---------------------- */
$stmt = mysqli_prepare($conn, "SELECT full_name FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user_row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$full_name = $user_row['full_name'] ?? 'Tenant';
$initials = strtoupper(substr($full_name, 0, 1));

/* ---------------------- HANDLE POST ACTIONS ---------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {

        case 'add_favorite':
            $house_id = (int) $_POST['house_id'];
            $check = mysqli_prepare($conn, "SELECT favorite_id FROM favorite WHERE tenant_id=? AND house_id=?");
            mysqli_stmt_bind_param($check, "ii", $tenant_id, $house_id);
            mysqli_stmt_execute($check);
            if (!mysqli_fetch_assoc(mysqli_stmt_get_result($check))) {
                $ins = mysqli_prepare($conn, "INSERT INTO favorite (tenant_id, house_id) VALUES (?, ?)");
                mysqli_stmt_bind_param($ins, "ii", $tenant_id, $house_id);
                mysqli_stmt_execute($ins);
            }
            header("Location: tenant_dashboard.php?tab=" . urlencode($_POST['return_tab'] ?? 'browse') . "&msg=fav_added");
            exit;

        case 'remove_favorite':
            $house_id = (int) $_POST['house_id'];
            $del = mysqli_prepare($conn, "DELETE FROM favorite WHERE tenant_id=? AND house_id=?");
            mysqli_stmt_bind_param($del, "ii", $tenant_id, $house_id);
            mysqli_stmt_execute($del);
            header("Location: tenant_dashboard.php?tab=" . urlencode($_POST['return_tab'] ?? 'favorites') . "&msg=fav_removed");
            exit;

        case 'send_interest':
            $house_id = (int) $_POST['house_id'];
            $msg_text = trim($_POST['message'] ?? '');
            if ($msg_text === '') $msg_text = 'I am interested in this property. Please share more details.';
            $ins = mysqli_prepare($conn, "INSERT INTO interest_request (tenant_id, house_id, message, request_status) VALUES (?, ?, ?, 'pending')");
            mysqli_stmt_bind_param($ins, "iis", $tenant_id, $house_id, $msg_text);
            mysqli_stmt_execute($ins);
            header("Location: tenant_dashboard.php?tab=requests&msg=interest_sent");
            exit;

        case 'add_review':
            $house_id = (int) $_POST['house_id'];
            $rating = max(1, min(5, (int) $_POST['rating']));
            $comment = trim($_POST['comment'] ?? '');
            $ins = mysqli_prepare($conn, "INSERT INTO review (house_id, tenant_id, rating, comment) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($ins, "iiis", $house_id, $tenant_id, $rating, $comment);
            mysqli_stmt_execute($ins);
            header("Location: tenant_dashboard.php?tab=reviews&msg=review_added");
            exit;

        case 'delete_review':
            $review_id = (int) $_POST['review_id'];
            $del = mysqli_prepare($conn, "DELETE FROM review WHERE review_id=? AND tenant_id=?");
            mysqli_stmt_bind_param($del, "ii", $review_id, $tenant_id);
            mysqli_stmt_execute($del);
            header("Location: tenant_dashboard.php?tab=reviews&msg=review_deleted");
            exit;
    }
}

/* ---------------------- ACTIVE TAB ---------------------- */
$allowed_tabs = ['overview', 'browse', 'favorites', 'requests', 'reviews'];
$tab = $_GET['tab'] ?? 'overview';
if (!in_array($tab, $allowed_tabs)) $tab = 'overview';

/* ---------------------- FLASH MESSAGE ---------------------- */
$flash_messages = [
    'fav_added'      => ['Added to your favorites.', 'success'],
    'fav_removed'    => ['Removed from your favorites.', 'success'],
    'interest_sent'  => ['Your interest request has been sent to the landlord.', 'success'],
    'review_added'   => ['Your review has been posted. Thank you!', 'success'],
    'review_deleted' => ['Review deleted.', 'success'],
];
$flash = $flash_messages[$_GET['msg'] ?? ''] ?? null;

/* ---------------------- STATS FOR OVERVIEW ---------------------- */
$fav_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM favorite WHERE tenant_id=$tenant_id"))['c'];
$req_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM interest_request WHERE tenant_id=$tenant_id"))['c'];
$review_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM review WHERE tenant_id=$tenant_id"))['c'];

/* ---------------------- BROWSE HOUSES ---------------------- */
$houses = [];
if ($tab === 'browse' || $tab === 'overview') {
    $sql = "SELECT h.*,
                   (SELECT image_url FROM house_image WHERE house_id = h.house_id LIMIT 1) AS image_url,
                   (SELECT COUNT(*) FROM favorite f WHERE f.house_id = h.house_id AND f.tenant_id = $tenant_id) AS is_fav
            FROM house h
            WHERE h.availability_status = 'available'
            ORDER BY h.created_at DESC";
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) $houses[] = $row;
}

/* ---------------------- FAVORITES ---------------------- */
$favorites = [];
if ($tab === 'favorites') {
    $sql = "SELECT h.*, f.favorite_id,
                   (SELECT image_url FROM house_image WHERE house_id = h.house_id LIMIT 1) AS image_url
            FROM favorite f
            JOIN house h ON h.house_id = f.house_id
            WHERE f.tenant_id = $tenant_id
            ORDER BY f.saved_at DESC";
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) $favorites[] = $row;
}

/* ---------------------- MY REQUESTS ---------------------- */
$requests = [];
if ($tab === 'requests' || $tab === 'overview') {
    $sql = "SELECT r.*, h.title, h.city, h.address,
                   (SELECT image_url FROM house_image WHERE house_id = h.house_id LIMIT 1) AS image_url
            FROM interest_request r
            JOIN house h ON h.house_id = r.house_id
            WHERE r.tenant_id = $tenant_id
            ORDER BY r.requested_at DESC";
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) $requests[] = $row;
}

/* ---------------------- MY REVIEWS + REVIEWABLE HOUSES ---------------------- */
$my_reviews = [];
$reviewable = [];
if ($tab === 'reviews') {
    $sql = "SELECT rv.*, h.title, h.city,
                   (SELECT image_url FROM house_image WHERE house_id = h.house_id LIMIT 1) AS image_url
            FROM review rv
            JOIN house h ON h.house_id = rv.house_id
            WHERE rv.tenant_id = $tenant_id
            ORDER BY rv.created_at DESC";
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) $my_reviews[] = $row;

    // Houses tenant had an accepted interest in, that they haven't reviewed yet
    $sql = "SELECT DISTINCT h.house_id, h.title, h.city,
                   (SELECT image_url FROM house_image WHERE house_id = h.house_id LIMIT 1) AS image_url
            FROM interest_request r
            JOIN house h ON h.house_id = r.house_id
            WHERE r.tenant_id = $tenant_id
              AND r.request_status = 'accepted'
              AND h.house_id NOT IN (
                  SELECT house_id FROM review WHERE tenant_id = $tenant_id
              )";
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) $reviewable[] = $row;
}

function h($str) { return htmlspecialchars($str ?? '', ENT_QUOTES); }
function img_or_placeholder($url) {
    return $url ? h($url) : null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Dashboard — Gharelu</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="tenant_dashboard.css">
</head>
<body>

<div class="dash-shell">

    <!-- SIDEBAR -->
    <aside class="dash-sidebar">
        <div class="dash-brand">
            <i class="fas fa-home"></i>
            <span>Gharelu</span>
        </div>

        <div class="dash-user">
            <div class="dash-user-avatar"><?= h($initials) ?></div>
            <div class="dash-user-info">
                <h4><?= h($full_name) ?></h4>
                <span>Tenant</span>
            </div>
        </div>

        <nav class="dash-nav">
            <a href="?tab=overview" class="<?= $tab === 'overview' ? 'active' : '' ?>">
                <i class="fas fa-gauge"></i><span>Overview</span>
            </a>
            <a href="?tab=browse" class="<?= $tab === 'browse' ? 'active' : '' ?>">
                <i class="fas fa-magnifying-glass"></i><span>Browse Houses</span>
            </a>
            <a href="?tab=favorites" class="<?= $tab === 'favorites' ? 'active' : '' ?>">
                <i class="fas fa-heart"></i><span>Favorites</span>
                <?php if ($fav_count > 0): ?><span class="badge-count"><?= $fav_count ?></span><?php endif; ?>
            </a>
            <a href="?tab=requests" class="<?= $tab === 'requests' ? 'active' : '' ?>">
                <i class="fas fa-paper-plane"></i><span>My Requests</span>
                <?php if ($req_count > 0): ?><span class="badge-count"><?= $req_count ?></span><?php endif; ?>
            </a>
            <a href="?tab=reviews" class="<?= $tab === 'reviews' ? 'active' : '' ?>">
                <i class="fas fa-star"></i><span>My Reviews</span>
            </a>
        </nav>

        <div class="dash-nav-footer">
            <a href="logout.php" class="dash-logout">
                <i class="fas fa-arrow-right-from-bracket"></i><span>Log Out</span>
            </a>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="dash-main">

        <?php if ($flash): ?>
            <div class="dash-alert <?= h($flash[1]) ?>">
                <i class="fas fa-circle-check"></i> <?= h($flash[0]) ?>
            </div>
        <?php endif; ?>

        <?php if ($tab === 'overview'): ?>
            <!-- ============ OVERVIEW ============ -->
            <div class="dash-topbar">
                <div>
                    <h1>Welcome back, <?= h(explode(' ', $full_name)[0]) ?> 👋</h1>
                    <p>Here's what's happening with your house hunt.</p>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-heart"></i></div>
                    <div>
                        <div class="stat-number"><?= $fav_count ?></div>
                        <div class="stat-label">Saved Favorites</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon gold"><i class="fas fa-paper-plane"></i></div>
                    <div>
                        <div class="stat-number"><?= $req_count ?></div>
                        <div class="stat-label">Interest Requests</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-star"></i></div>
                    <div>
                        <div class="stat-number"><?= $review_count ?></div>
                        <div class="stat-label">Reviews Written</div>
                    </div>
                </div>
            </div>

            <div class="dash-section-title">
                <h2>Recently Listed Houses <span>— tap the heart to save one</span></h2>
            </div>
            <div class="properties-grid">
                <?php foreach (array_slice($houses, 0, 3) as $house): ?>
                    <?php include __DIR__ . '/tenant_house_card.php'; ?>
                <?php endforeach; ?>
                <?php if (empty($houses)): ?>
                    <div class="empty-state" style="grid-column:1/-1;">
                        <i class="fas fa-house-circle-xmark"></i>
                        <h3>No houses available right now</h3>
                        <p>Check back soon — new listings are added regularly.</p>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($tab === 'browse'): ?>
            <!-- ============ BROWSE ============ -->
            <div class="dash-topbar">
                <div>
                    <h1>Browse Houses</h1>
                    <p>Find your next home from <?= count($houses) ?> available listings.</p>
                </div>
            </div>

            <div class="properties-grid">
                <?php foreach ($houses as $house): ?>
                    <?php include __DIR__ . '/tenant_house_card.php'; ?>
                <?php endforeach; ?>
            </div>
            <?php if (empty($houses)): ?>
                <div class="empty-state">
                    <i class="fas fa-house-circle-xmark"></i>
                    <h3>No houses available right now</h3>
                    <p>Check back soon — new listings are added regularly.</p>
                </div>
            <?php endif; ?>

        <?php elseif ($tab === 'favorites'): ?>
            <!-- ============ FAVORITES ============ -->
            <div class="dash-topbar">
                <div>
                    <h1>My Favorites</h1>
                    <p>Houses you've saved for later.</p>
                </div>
            </div>

            <div class="properties-grid">
                <?php foreach ($favorites as $house): ?>
                    <div class="property-card">
                        <div class="property-image">
                            <?php if ($img = img_or_placeholder($house['image_url'])): ?>
                                <img src="<?= $img ?>" alt="<?= h($house['title']) ?>">
                            <?php else: ?>
                                <div class="no-img"><i class="fas fa-image"></i></div>
                            <?php endif; ?>
                            <span class="property-status <?= h($house['availability_status']) ?>"><?= h($house['availability_status']) ?></span>
                        </div>
                        <div class="property-details">
                            <h3><?= h($house['title']) ?></h3>
                            <div class="property-location"><i class="fas fa-location-dot"></i> <?= h($house['city']) ?></div>
                            <div class="property-features">
                                <span><i class="fas fa-bed"></i><?= (int)$house['bedrooms'] ?> Bed</span>
                                <span><i class="fas fa-bath"></i><?= (int)$house['bathrooms'] ?> Bath</span>
                            </div>
                            <div class="property-price">Rs <?= number_format($house['price']) ?> <small>/ month</small></div>
                            <div class="property-actions">
                                <form method="POST" style="flex:1;">
                                    <input type="hidden" name="action" value="remove_favorite">
                                    <input type="hidden" name="house_id" value="<?= (int)$house['house_id'] ?>">
                                    <input type="hidden" name="return_tab" value="favorites">
                                    <button type="submit" class="btn-danger-outline" style="width:100%;">
                                        <i class="fas fa-heart-crack"></i> Remove
                                    </button>
                                </form>
                                <button class="btn-primary" onclick="openInterestModal(<?= (int)$house['house_id'] ?>, '<?= h(addslashes($house['title'])) ?>')">
                                    <i class="fas fa-paper-plane"></i> Contact
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (empty($favorites)): ?>
                <div class="empty-state">
                    <i class="fas fa-heart-crack"></i>
                    <h3>No favorites yet</h3>
                    <p>Browse houses and tap the heart icon to save the ones you like.</p>
                    <a href="?tab=browse" class="btn-primary"><i class="fas fa-magnifying-glass"></i> Browse Houses</a>
                </div>
            <?php endif; ?>

        <?php elseif ($tab === 'requests'): ?>
            <!-- ============ MY REQUESTS ============ -->
            <div class="dash-topbar">
                <div>
                    <h1>My Requests</h1>
                    <p>Track the status of the interest you've sent to landlords.</p>
                </div>
            </div>

            <div class="request-list">
                <?php foreach ($requests as $req): ?>
                    <div class="request-card">
                        <div class="request-thumb">
                            <?php if ($img = img_or_placeholder($req['image_url'])): ?>
                                <img src="<?= $img ?>" alt="">
                            <?php else: ?>
                                <div class="no-img" style="height:100%;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="request-body">
                            <h4><?= h($req['title']) ?></h4>
                            <div class="request-loc"><i class="fas fa-location-dot"></i> <?= h($req['city']) ?></div>
                            <div class="request-msg">"<?= h($req['message']) ?>"</div>
                        </div>
                        <div class="request-meta">
                            <span class="status-badge <?= h($req['request_status']) ?>"><?= h($req['request_status']) ?></span>
                            <div class="request-date"><?= date('M j, Y', strtotime($req['requested_at'])) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (empty($requests)): ?>
                <div class="empty-state">
                    <i class="fas fa-paper-plane"></i>
                    <h3>No requests sent yet</h3>
                    <p>When you find a house you like, send the landlord an interest request.</p>
                    <a href="?tab=browse" class="btn-primary"><i class="fas fa-magnifying-glass"></i> Browse Houses</a>
                </div>
            <?php endif; ?>

        <?php elseif ($tab === 'reviews'): ?>
            <!-- ============ MY REVIEWS ============ -->
            <div class="dash-topbar">
                <div>
                    <h1>My Reviews</h1>
                    <p>Share your experience and manage reviews you've written.</p>
                </div>
            </div>

            <?php if (!empty($reviewable)): ?>
                <div class="dash-section-title"><h2>Houses You Can Review <span>— based on accepted requests</span></h2></div>
                <?php foreach ($reviewable as $rh): ?>
                    <div class="reviewable-card">
                        <div class="request-thumb">
                            <?php if ($img = img_or_placeholder($rh['image_url'])): ?>
                                <img src="<?= $img ?>" alt="">
                            <?php else: ?>
                                <div class="no-img" style="height:100%;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="request-body">
                            <h4><?= h($rh['title']) ?></h4>
                            <div class="request-loc"><i class="fas fa-location-dot"></i> <?= h($rh['city']) ?></div>
                        </div>
                        <button class="btn-gold" onclick="openReviewModal(<?= (int)$rh['house_id'] ?>, '<?= h(addslashes($rh['title'])) ?>')">
                            <i class="fas fa-star"></i> Write Review
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="dash-section-title" style="margin-top:2rem;"><h2>Reviews You've Written</h2></div>
            <?php foreach ($my_reviews as $rv): ?>
                <div class="review-card">
                    <div class="review-card-top">
                        <div>
                            <h4><?= h($rv['title']) ?></h4>
                            <div class="stars">
                                <?php for ($i=1;$i<=5;$i++): ?>
                                    <i class="fa<?= $i <= $rv['rating'] ? 's' : 'r' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="review-date"><?= date('M j, Y', strtotime($rv['created_at'])) ?></div>
                    </div>
                    <p class="comment"><?= h($rv['comment']) ?></p>
                    <div class="review-card-actions">
                        <form method="POST" onsubmit="return confirm('Delete this review?');">
                            <input type="hidden" name="action" value="delete_review">
                            <input type="hidden" name="review_id" value="<?= (int)$rv['review_id'] ?>">
                            <button type="submit" class="btn-danger-outline"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

<?php if (empty($my_reviews) && empty($reviewable)): ?>
    <div class="empty-state">
        <i class="fas fa-star"></i>
        <h3>No reviews yet</h3>
        <p>Once a landlord accepts your interest request, you'll be able to leave a review here.</p>
    </div>
<?php endif; ?>

<?php endif; ?>

</main>
</div>
</div>

<!-- ============ INTEREST MODAL ============ -->
<div class="modal-overlay" id="interestModal">
    <div class="modal-box">
        <div class="modal-box-header">
            <h3 id="interestModalTitle">Send Interest</h3>
            <button class="modal-close" onclick="closeModal('interestModal')">&times;</button>
        </div>
        <form method="POST" class="modal-box-body">
            <input type="hidden" name="action" value="send_interest">
            <input type="hidden" name="house_id" id="interestHouseId">
            <div class="form-group">
                <label>Message to landlord</label>
                <textarea name="message" rows="4" placeholder="I'd like to schedule a visit this weekend..."></textarea>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;">
                <i class="fas fa-paper-plane"></i> Send Request
            </button>
        </form>
    </div>
</div>

<!-- ============ REVIEW MODAL ============ -->
<div class="modal-overlay" id="reviewModal">
    <div class="modal-box">
        <div class="modal-box-header">
            <h3 id="reviewModalTitle">Write a Review</h3>
            <button class="modal-close" onclick="closeModal('reviewModal')">&times;</button>
        </div>
        <form method="POST" class="modal-box-body">
            <input type="hidden" name="action" value="add_review">
            <input type="hidden" name="house_id" id="reviewHouseId">
            <input type="hidden" name="rating" id="reviewRatingInput" value="5">
            <div class="form-group">
                <label>Your rating</label>
                <div class="rating-input" id="ratingStars">
                    <i class="fas fa-star active" data-val="1"></i>
                    <i class="fas fa-star active" data-val="2"></i>
                    <i class="fas fa-star active" data-val="3"></i>
                    <i class="fas fa-star active" data-val="4"></i>
                    <i class="fas fa-star active" data-val="5"></i>
                </div>
            </div>
            <div class="form-group">
                <label>Your review</label>
                <textarea name="comment" rows="4" placeholder="Share your experience with this property..." required></textarea>
            </div>
            <button type="submit" class="btn-gold" style="width:100%;">
                <i class="fas fa-star"></i> Post Review
            </button>
        </form>
    </div>
</div>

<script>
function openInterestModal(houseId, title) {
    document.getElementById('interestHouseId').value = houseId;
    document.getElementById('interestModalTitle').textContent = 'Send Interest — ' + title;
    document.getElementById('interestModal').classList.add('open');
}
function openReviewModal(houseId, title) {
    document.getElementById('reviewHouseId').value = houseId;
    document.getElementById('reviewModalTitle').textContent = 'Review — ' + title;
    document.getElementById('reviewModal').classList.add('open');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}
document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) overlay.classList.remove('open');
    });
});

// Star rating picker
const stars = document.querySelectorAll('#ratingStars i');
const ratingInput = document.getElementById('reviewRatingInput');
stars.forEach(function(star) {
    star.addEventListener('click', function() {
        const val = parseInt(star.dataset.val);
        ratingInput.value = val;
        stars.forEach(function(s) {
            s.classList.toggle('active', parseInt(s.dataset.val) <= val);
        });
    });
});
</script>

</body>
</html>