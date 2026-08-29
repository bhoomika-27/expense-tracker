<?php
/**
 * Authentication helpers.
 * Include this file at the top of any page that needs to know about the
 * logged-in user, or that must be protected from anonymous access.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Returns true if a user is currently logged in. */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/** Returns the logged-in user's ID, or null if not logged in. */
function currentUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

/** Returns the logged-in user's display name, or null if not logged in. */
function currentUserName(): ?string
{
    return $_SESSION['user_name'] ?? null;
}

/**
 * Call this at the top of any page that requires the user to be logged in.
 * Sends anonymous visitors to the login page.
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}
