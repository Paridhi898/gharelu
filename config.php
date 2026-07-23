<?php
session_start();

function db_connect() {
    $conn = mysqli_connect('localhost', 'root', '', 'gharelu_db');
    if (!$conn) {
        die('Database connection failed: ' . mysqli_connect_error());
    }
    mysqli_set_charset($conn, 'utf8mb4');
    return $conn;
}

function esc($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function require_login() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

function redirect_role_home() {
    $role = $_SESSION['user_type'] ?? '';

    if ($role === '') {
        $user = current_user();
        $role = $user['user_type'] ?? '';
        if ($role !== '') {
            $_SESSION['user_type'] = $role;
        }
    }

    if ($role === 'admin') {
        header('Location: admin_dashboard.php');
    } elseif ($role === 'landlord') {
        header('Location: landlord_dashboard.php');
    } elseif ($role === 'tenant') {
        header('Location: tenant_dashboard.php');
    } else {
        header('Location: login.php');
    }
    exit();
}

function require_role($expectedRole) {
    require_login();

    $currentUser = current_user();
    $actualRole = $currentUser['user_type'] ?? ($_SESSION['user_type'] ?? '');

    if ($actualRole !== '') {
        $_SESSION['user_type'] = $actualRole;
    }

    if ($actualRole !== $expectedRole) {
        redirect_role_home();
    }
}

function current_user() {
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $conn = db_connect();
    $id = intval($_SESSION['user_id']);
    $stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE id = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    return $user;
}
