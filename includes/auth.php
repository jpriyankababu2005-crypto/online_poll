<?php

require_once __DIR__ . '/../config.php';

function startAppSession()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function loginUser($user)
{
    $_SESSION['user_id'] = objectIdToString($user['_id']);
    $_SESSION['role'] = $user['role'] ?? 'user';
}

function logoutUser()
{
    $_SESSION = [];
    session_destroy();
}

function requireRole($role)
{
    $currentRole = $_SESSION['role'] ?? null;
    if ($currentRole !== $role) {
        header('Location: index.php');
        exit();
    }
}

function getSessionUserId()
{
    return oid($_SESSION['user_id'] ?? '');
}

?>
