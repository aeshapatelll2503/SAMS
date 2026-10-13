<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

define('DB_HOST', 'localhost');
define('DB_NAME', 'structurealauthoritymappingsystem');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

/**
 * Determine user role based on email address format:
 * - xyz12.admin@gmail.com, admin@... -> 'admin'
 * - xyz12.manager@gmail.com, manager@... -> 'manager'
 * - simple / regular email (e.g. xyz12@gmail.com) -> 'viewer'
 */
function get_role_from_email($email) {
    $email = strtolower(trim((string)$email));
    $parts = explode('@', $email);
    $localPart = $parts[0] ?? '';

    if ($localPart === 'admin' || preg_match('/(^|\.)admin(\.|$)/i', $localPart)) {
        return 'admin';
    }
    if ($localPart === 'manager' || preg_match('/(^|\.)manager(\.|$)/i', $localPart)) {
        return 'manager';
    }
    return 'viewer';
}

/**
 * Get current session user role ('admin', 'manager', 'viewer')
 */
function get_current_user_role() {
    return strtolower($_SESSION['user_role'] ?? $_SESSION['role'] ?? 'viewer');
}

function is_admin() {
    return get_current_user_role() === 'admin';
}

function is_manager() {
    return get_current_user_role() === 'manager';
}

function is_viewer() {
    return get_current_user_role() === 'viewer';
}

function can_edit() {
    return in_array(get_current_user_role(), ['admin', 'manager']);
}

/**
 * Render visual badge for a given role or current logged-in role
 */
function render_role_badge($role = null) {
    $r = strtolower($role ?? get_current_user_role());
    $badgeStyle = 'bg-emerald-100 text-emerald-800 border-emerald-300';
    $roleIcon = 'visibility';
    $label = 'Viewer';
    
    if ($r === 'admin') {
        $badgeStyle = 'bg-purple-100 text-purple-800 border-purple-300';
        $roleIcon = 'shield';
        $label = 'Admin';
    } elseif ($r === 'manager') {
        $badgeStyle = 'bg-blue-100 text-blue-800 border-blue-300';
        $roleIcon = 'manage_accounts';
        $label = 'Manager';
    }

    return '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider border ' . $badgeStyle . '" title="Role: ' . $label . '">' .
           '<span class="material-symbols-outlined text-[14px]">' . $roleIcon . '</span>' .
           $label .
           '</span>';
}
?>