<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';

requireLogin();

$errors = [];
$amount = '';
$category = 'Food';
$description = '';
$date = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount'] ?? '');
    $category = $_POST['category'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $date = $_POST['expense_date'] ?? '';

    $errors = validateExpenseInput($amount, $category, $date);

    if (mb_strlen($description) > 255) {
        $errors[] = 'Description is too long (max 255 characters).';
    }

    if (empty($errors)) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO expenses (user_id, amount, category, description, expense_date) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([currentUserId(), (float)$amount, $category, $description, $date]);

        header('Location: expenses.php');
        exit;
    }
}

$pageTitle = 'Add Expense';
require __DIR__ . '/includes/header.php';
?>

<div class="card p-4" style="max-width: 560px; margin: 0 auto;">
    <h3 class="mb-3">Add Expense</h3>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= h($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="add_expense.php" novalidate>
        <div class="mb-3">
            <label for="amount" class="form-label">Amount (₹)</label>
            <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" value="<?= h($amount) ?>" required>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" name="category" required>
                <?php foreach (expenseCategories() as $cat): ?>
                    <option value="<?= h($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= h($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input type="text" class="form-control" id="description" name="description" value="<?= h($description) ?>" maxlength="255" placeholder="e.g. Lunch with friends">
        </div>
        <div class="mb-3">
            <label for="expense_date" class="form-label">Date</label>
            <input type="date" class="form-control" id="expense_date" name="expense_date" value="<?= h($date) ?>" required>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">Save Expense</button>
            <a href="expenses.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
