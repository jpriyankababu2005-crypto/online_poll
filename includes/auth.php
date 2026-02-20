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
    $rawId = $user['_id'] ?? '';
    if ($rawId instanceof MongoDB\BSON\ObjectId) {
        $_SESSION['user_id'] = (string)$rawId;
    } else {
        $_SESSION['user_id'] = trim((string)$rawId);
    }

    $role = strtolower(trim((string)($user['role'] ?? 'user')));
    $_SESSION['role'] = $role !== '' ? $role : 'user';
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
    $rawId = trim((string)($_SESSION['user_id'] ?? ''));
    if ($rawId === '') {
        return null;
    }

    $objectId = oid($rawId);
    if ($objectId !== null) {
        return $objectId;
    }

    return $rawId;
}
