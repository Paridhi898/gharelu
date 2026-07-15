<?php

require_once __DIR__ . '/config.php';

if (!empty($_SESSION['user_id'])) {
    redirect_role_home();
}

$errors = [];
$username = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = 'Please enter both username and password.';
    } else {
        $conn = db_connect();
        $stmt = mysqli_prepare($conn, 'SELECT id, username, password, user_type FROM users WHERE username = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            if ($user['user_type'] === 'landlord') {
                $verifyStmt = mysqli_prepare($conn, 'SELECT verification_status FROM landlord WHERE user_id = ? LIMIT 1');
                if ($verifyStmt) {
                    mysqli_stmt_bind_param($verifyStmt, 'i', $user['id']);
                    mysqli_stmt_execute($verifyStmt);
                    $verifyResult = mysqli_stmt_get_result($verifyStmt);
                    $landlordRow = mysqli_fetch_assoc($verifyResult);
                } else {
                    $landlordRow = null;
                }

                if (!$landlordRow || $landlordRow['verification_status'] !== 'verified') {
                    $errors[] = 'Your landlord account is pending verification. Please wait for admin approval.';
                    mysqli_close($conn);
                } elseif (password_verify($password, $user['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_type'] = $user['user_type'];
                    mysqli_close($conn);
                    redirect_role_home();
                } else {
                    $errors[] = 'Incorrect password.';
                    mysqli_close($conn);
                }
            } elseif (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user['user_type'];
                mysqli_close($conn);
                redirect_role_home();
            } else {
                $errors[] = 'Incorrect password.';
                mysqli_close($conn);
            }
        } else {
            $errors[] = 'User not found.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login</title>

    <link rel="stylesheet" href="auth.css">

</head>

<body>

    <div class="login-container">

        <div class="top-section">

            <div class="icon">🏠</div>

            <h1>Welcome Back</h1>

            <p>Login to your Gharelu account</p>

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
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>

                <div class="form-group">

                    <label>Password</label>

                    <input type="password" name="password" required>

                </div>

                <button type="submit" class="login-btn">
                    LOGIN
                </button>

            </form>

            <div class="register-text">

                Don't have an account?

                <a href="signup.php">Register here</a>

            </div>

        </div>

    </div>

</body>

</html>