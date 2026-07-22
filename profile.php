<?php
require_once __DIR__ . '/config.php';
require_role('landlord');

$user = current_user();
if (!$user) {
    redirect_role_home();
}

$landlordUserId = intval($user['id']);
$errors = [];
$success = '';

$conn = db_connect();
$landlordStmt = mysqli_prepare(
    $conn,
    'SELECT landlord_id, citizenship_no, land_ownership_certificate_no, profile_image, citizenship_image, verification_status, verified_at FROM landlord WHERE user_id = ? LIMIT 1'
);
if ($landlordStmt) {
    mysqli_stmt_bind_param($landlordStmt, 'i', $landlordUserId);
    mysqli_stmt_execute($landlordStmt);
    $landlordResult = mysqli_stmt_get_result($landlordStmt);
    $landlordRow = mysqli_fetch_assoc($landlordResult) ?: null;
    mysqli_stmt_close($landlordStmt);
} else {
    $landlordRow = null;
}

if (!$landlordRow) {
    $insertLandlordStmt = mysqli_prepare($conn, 'INSERT INTO landlord (user_id, verification_status) VALUES (?, ?)');
    if ($insertLandlordStmt) {
        $defaultStatus = 'pending';
        mysqli_stmt_bind_param($insertLandlordStmt, 'is', $landlordUserId, $defaultStatus);
        mysqli_stmt_execute($insertLandlordStmt);
        mysqli_stmt_close($insertLandlordStmt);
        $landlordRow = [
            'landlord_id' => mysqli_insert_id($conn),
            'citizenship_no' => '',
            'land_ownership_certificate_no' => '',
            'profile_image' => '',
            'citizenship_image' => '',
            'verification_status' => 'pending',
            'verified_at' => null,
        ];
    } else {
        $errors[] = 'Unable to load landlord profile information.';
        $landlordRow = [
            'landlord_id' => 0,
            'citizenship_no' => '',
            'land_ownership_certificate_no' => '',
            'profile_image' => '',
            'citizenship_image' => '',
            'verification_status' => 'pending',
            'verified_at' => null,
        ];
    }
}

function saveUploadedImage(array $file, string $dir, array &$errors): ?string
{
    if (empty($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Upload failed for ' . htmlspecialchars($file['name']) . '.';
        return null;
    }

    $tmpName = $file['tmp_name'];
    if (!is_uploaded_file($tmpName) || getimagesize($tmpName) === false) {
        $errors[] = 'The selected file for ' . htmlspecialchars($file['name']) . ' is not a valid image.';
        return null;
    }

    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        $errors[] = 'Unable to create upload directory.';
        return null;
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $extension = strtolower($extension ?: 'jpg');
    $safeName = time() . '_' . bin2hex(random_bytes(8)) . '.' . preg_replace('/[^a-z0-9]+/', '', $extension);
    $destination = $dir . '/' . $safeName;

    if (!move_uploaded_file($tmpName, $destination)) {
        $errors[] = 'Failed to save uploaded file.';
        return null;
    }

    return str_replace('\\', '/', 'uploads/' . basename($dir) . '/' . $safeName);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phoneNumber = trim($_POST['phone_number'] ?? '');
    $citizenshipId = trim($_POST['citizenship_id'] ?? '');
    $citizenshipNo = trim($_POST['citizenship_no'] ?? '');
    $ownershipNo = trim($_POST['land_ownership_certificate_no'] ?? '');

    if ($fullName === '' || $email === '' || $citizenshipId === '') {
        $errors[] = 'Full name, email, and citizenship ID are required.';
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($phoneNumber !== '' && !preg_match('/^[0-9]{10,15}$/', $phoneNumber)) {
        $errors[] = 'Phone number must contain 10 to 15 digits.';
    }

    $profileImagePath = null;
    $citizenshipImagePath = null;

    if (empty($errors)) {
        $profileImagePath = saveUploadedImage($_FILES['profile_image'] ?? [], __DIR__ . '/uploads/profile', $errors);
        $citizenshipImagePath = saveUploadedImage($_FILES['citizenship_image'] ?? [], __DIR__ . '/uploads/citizenship', $errors);
    }

    if (empty($errors)) {
        $emailCheckStmt = mysqli_prepare($conn, 'SELECT id FROM users WHERE (email = ? OR phone_number = ?) AND id != ? LIMIT 1');
        if ($emailCheckStmt) {
            mysqli_stmt_bind_param($emailCheckStmt, 'ssi', $email, $phoneNumber, $landlordUserId);
            mysqli_stmt_execute($emailCheckStmt);
            $result = mysqli_stmt_get_result($emailCheckStmt);
            if ($existing = mysqli_fetch_assoc($result)) {
                $errors[] = 'Email or phone number is already in use by another account.';
            }
            mysqli_stmt_close($emailCheckStmt);
        }
    }

    if (empty($errors)) {
        mysqli_begin_transaction($conn);

        $userUpdateStmt = mysqli_prepare(
            $conn,
            'UPDATE users SET full_name = ?, email = ?, phone_number = ?, citizenship_id = ? WHERE id = ?'
        );
        if (!$userUpdateStmt) {
            $errors[] = 'Unable to update user profile.';
        } else {
            mysqli_stmt_bind_param($userUpdateStmt, 'ssssi', $fullName, $email, $phoneNumber, $citizenshipId, $landlordUserId);
            if (!mysqli_stmt_execute($userUpdateStmt)) {
                $errors[] = 'Unable to update user information.';
            }
            mysqli_stmt_close($userUpdateStmt);
        }
    }

    if (empty($errors)) {
        $landlordUpdateSql = 'UPDATE landlord SET citizenship_no = ?, land_ownership_certificate_no = ?';
        if ($profileImagePath !== null) {
            $landlordUpdateSql .= ', profile_image = ?';
        }
        if ($citizenshipImagePath !== null) {
            $landlordUpdateSql .= ', citizenship_image = ?';
        }
        $landlordUpdateSql .= ' WHERE user_id = ?';

        $landlordUpdateStmt = mysqli_prepare($conn, $landlordUpdateSql);
        if ($landlordUpdateStmt) {
            if ($profileImagePath !== null && $citizenshipImagePath !== null) {
                mysqli_stmt_bind_param($landlordUpdateStmt, 'ssssi', $citizenshipNo, $ownershipNo, $profileImagePath, $citizenshipImagePath, $landlordUserId);
            } elseif ($profileImagePath !== null) {
                mysqli_stmt_bind_param($landlordUpdateStmt, 'sssi', $citizenshipNo, $ownershipNo, $profileImagePath, $landlordUserId);
            } elseif ($citizenshipImagePath !== null) {
                mysqli_stmt_bind_param($landlordUpdateStmt, 'sssi', $citizenshipNo, $ownershipNo, $citizenshipImagePath, $landlordUserId);
            } else {
                mysqli_stmt_bind_param($landlordUpdateStmt, 'ssi', $citizenshipNo, $ownershipNo, $landlordUserId);
            }

            if (!mysqli_stmt_execute($landlordUpdateStmt)) {
                $errors[] = 'Unable to update landlord profile details.';
            }
            mysqli_stmt_close($landlordUpdateStmt);
        } else {
            $errors[] = 'Unable to prepare landlord profile update.';
        }
    }

    if (empty($errors)) {
        mysqli_commit($conn);
        $success = 'Profile updated successfully.';
        $_SESSION['username'] = $user['username'];
        $user['full_name'] = $fullName;
        $user['email'] = $email;
        $user['phone_number'] = $phoneNumber;
        $user['citizenship_id'] = $citizenshipId;
        $landlordRow['citizenship_no'] = $citizenshipNo;
        $landlordRow['land_ownership_certificate_no'] = $ownershipNo;
        if ($profileImagePath !== null) {
            $landlordRow['profile_image'] = $profileImagePath;
        }
        if ($citizenshipImagePath !== null) {
            $landlordRow['citizenship_image'] = $citizenshipImagePath;
        }
    } else {
        mysqli_rollback($conn);
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Landlord Profile</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Manrope:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="main" style="margin-left:220px;">
  <header class="topbar">
    <h2>Landlord Profile</h2>
    <div class="topbar-right">
      <a class="btn btn-ghost" href="landlord_dashboard.php">← Dashboard</a>
    </div>
  </header>
  <div class="content">
    <div class="form-card" style="max-width:760px;">
      <?php if (!empty($errors)): ?>
        <div class="error-message" style="border:1px solid #f1a0a0; background:#fff5f5; padding:14px; border-radius:12px; margin-bottom:16px;">
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo esc($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php elseif (!empty($success)): ?>
        <div class="success-message" style="border:1px solid #b7f5c1; background:#ecffed; padding:14px; border-radius:12px; margin-bottom:16px;">
          <?php echo esc($success); ?>
        </div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <div class="form-grid">
          <div class="fg full"><label>Full Name</label><input type="text" name="full_name" value="<?php echo esc($user['full_name'] ?? ''); ?>" required></div>
          <div class="fg"><label>Username</label><input type="text" value="<?php echo esc($user['username'] ?? ''); ?>" readonly></div>
          <div class="fg"><label>Email</label><input type="email" name="email" value="<?php echo esc($user['email'] ?? ''); ?>" required></div>
          <div class="fg"><label>Phone Number</label><input type="text" name="phone_number" value="<?php echo esc($user['phone_number'] ?? ''); ?>" pattern="[0-9]{10,15}"></div>
          <div class="fg full"><label>Citizenship ID</label><input type="text" name="citizenship_id" value="<?php echo esc($user['citizenship_id'] ?? ''); ?>" required></div>
          <div class="fg full"><label>Citizenship Number</label><input type="text" name="citizenship_no" value="<?php echo esc($landlordRow['citizenship_no'] ?? ''); ?>"></div>
          <div class="fg full"><label>Land Ownership Certificate</label><input type="text" name="land_ownership_certificate_no" value="<?php echo esc($landlordRow['land_ownership_certificate_no'] ?? ''); ?>"></div>
          <div class="fg full"><label>Account Status</label><input type="text" value="<?php echo esc(ucfirst($landlordRow['verification_status'] ?? 'pending')); ?>" readonly></div>
          <div class="fg full"><label>Verified At</label><input type="text" value="<?php echo esc($landlordRow['verified_at'] ?? 'Not verified'); ?>" readonly></div>
          <div class="fg full">
            <label>Profile Image</label>
            <?php if (!empty($landlordRow['profile_image'])): ?>
              <img src="<?php echo esc($landlordRow['profile_image']); ?>" alt="Profile" style="max-width:160px; border-radius:12px; display:block; margin-bottom:10px;">
            <?php endif; ?>
            <input type="file" name="profile_image" accept="image/*">
          </div>
          <div class="fg full">
            <label>Citizenship Image</label>
            <?php if (!empty($landlordRow['citizenship_image'])): ?>
              <img src="<?php echo esc($landlordRow['citizenship_image']); ?>" alt="Citizenship" style="max-width:160px; border-radius:12px; display:block; margin-bottom:10px;">
            <?php endif; ?>
            <input type="file" name="citizenship_image" accept="image/*">
          </div>
        </div>
        <br>
        <button class="btn btn-primary" type="submit">Save Profile</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
