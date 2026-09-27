<?php
/**
 * Session bootstrap + auth/role helper functions.
 * Every protected page includes this file first.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function current_user(): ?array {
    if (!is_logged_in()) return null;
    return [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role'  => $_SESSION['user_role'],
    ];
}

function is_admin(): bool {
    return is_logged_in() && $_SESSION['user_role'] === 'admin';
}

/** Redirect to login if not authenticated. Call at the top of every protected page. */
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ../auth/login.php');
        exit;
    }
}

/** Redirect non-admins away from admin-only actions (add/edit/delete). */
function require_admin(): void {
    require_login();
    if (!is_admin()) {
        header('Location: ../dashboard.php?error=forbidden');
        exit;
    }
}

/** Simple helper to escape output and avoid repeating htmlspecialchars() everywhere. */
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
