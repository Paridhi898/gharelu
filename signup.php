<?php

require_once __DIR__ . '/config.php';

$errors = [];
$full_name = '';
$username = '';
$email = '';
$phone_number = '';
$citizenship_id = '';
$user_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $citizenship_id = trim($_POST['citizenship_id'] ?? '');
    $user_type = trim($_POST['user_type'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($full_name === '' || $username === '' || $email === '' || $user_type === '' || $password === '') {
        $errors[] = 'Please fill in all required fields.';
    }

    if (!in_array($user_type, ['tenant', 'landlord'], true)) {
        $errors[] = 'Please select a valid user type.';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }

    if ($phone_number !== '' && !preg_match('/^[0-9]{10}$/', $phone_number)) {
        $errors[] = 'Phone number must be exactly 10 digits.';
    }

    if (empty($errors)) {
        $conn = db_connect();

        $stmt = mysqli_prepare($conn, 'SELECT username, email, phone_number FROM users WHERE username = ? OR email = ? OR phone_number = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'sss', $username, $email, $phone_number);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $existing = mysqli_fetch_assoc($result);
            if ($existing['username'] === $username) {
                $errors[] = 'Username already exists.';
            } elseif ($existing['email'] === $email) {
                $errors[] = 'Email already exists.';
            } elseif ($existing['phone_number'] === $phone_number) {
                $errors[] = 'Phone number already exists.';
            }
        }

        if (empty($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $verification_status = $user_type === 'tenant' ? 'verified' : 'pending';
            $stmt = mysqli_prepare($conn, 'INSERT INTO users (full_name, username, email, phone_number, citizenship_id, user_type, password) VALUES (?, ?, ?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'sssssss', $full_name, $username, $email, $phone_number, $citizenship_id, $user_type, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                $newUserId = mysqli_insert_id($conn);

                if ($user_type === 'landlord') {
                    $landlordStmt = mysqli_prepare($conn, 'INSERT INTO landlord (user_id, verification_status) VALUES (?, ?)');
                    if ($landlordStmt) {
                        mysqli_stmt_bind_param($landlordStmt, 'is', $newUserId, $verification_status);
                        mysqli_stmt_execute($landlordStmt);
                    }
                }

                mysqli_close($conn);
                header('Location: login.php');
                exit();
            }

            $errors[] = 'Registration failed. Please try again.';
        }

        mysqli_close($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Signup</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="register-container">

    <div class="top-section">

        <div class="icon">🏠</div>

        <h1>Join Gharelu</h1>

        <p>Create your account and start renting</p>

    </div>

    <div class="form-section">

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Enter full name" value="<?php echo htmlspecialchars($full_name); ?>" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" minlength="10" maxlength="10" placeholder="98XXXXXXXX" value="<?php echo htmlspecialchars($phone_number); ?>">
            </div>

            <div class="form-group">
                <label>Citizenship ID</label>
                <input type="text" name="citizenship_id" placeholder="Eg:04-01-73-02257" minlength="9" required value="<?php echo htmlspecialchars($citizenship_id); ?>">
            </div>

            <div class="form-group">
                <label>User Type</label>

                <select name="user_type" required>

                    <option value="">Select User Type</option>

                    <option value="tenant" <?php echo $user_type === 'tenant' ? 'selected' : ''; ?>>Tenant</option>

                    <option value="landlord" <?php echo $user_type === 'landlord' ? 'selected' : ''; ?>>Landlord</option>

                  

                </select>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password"  placeholder="Confirm your password">
            </div>

            <button type="submit" class="register-btn">
                REGISTER
            </button>

        </form>

        <div class="login-text">

            Already have an account?

            <a href="login.php">Login here</a>

        </div>

    </div>

</div>

</body>
</html>