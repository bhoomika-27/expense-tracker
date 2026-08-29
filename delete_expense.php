<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';

requireLogin();

$pdo = getDbConnection();
$userId = currentUserId();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: expenses.php');
    exit;
}

// Scope the DELETE by user_id too, so a user can never delete an expense
// that belongs to someone else, even if they guess/change the id.
$stmt = $pdo->prepare('DELETE FROM expenses WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $userId]);

header('Location: expenses.php');
exit;
