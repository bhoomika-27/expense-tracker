<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';

requireLogin();

$pdo = getDbConnection();
$userId = currentUserId();

// ---- Read & lightly validate filter inputs -------------------------------
$category = $_GET['category'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

if ($category !== '' && !in_array($category, expenseCategories(), true)) {
    $category = '';
}
if ($from !== '' && !DateTime::createFromFormat('Y-m-d', $from)) {
    $from = '';
}
if ($to !== '' && !DateTime::createFromFormat('Y-m-d', $to)) {
    $to = '';
}

// ---- Build query safely with bound parameters ----------------------------
$sql = 'SELECT * FROM expenses WHERE user_id = ?';
$params = [$userId];

if ($category !== '') {
    $sql .= ' AND category = ?';
    $params[] = $category;
}
if ($from !== '') {
    $sql .= ' AND expense_date >= ?';
    $params[] = $from;
}
if ($to !== '') {
    $sql .= ' AND expense_date <= ?';
    $params[] = $to;
}

$sql .= ' ORDER BY expense_date DESC, id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$expenses = $stmt->fetchAll();

$filteredTotal = array_sum(array_column($expenses, 'amount'));

$pageTitle = 'Expenses';
require __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Your Expenses</h2>
    <a href="add_expense.php" class="btn btn-primary">+ Add Expense</a>
</div>

<div class="card p-3 mb-4">
    <form method="get" action="expenses.php" class="row g-2 align-items-end">
        <div class="col-sm-3">
            <label class="form-label small mb-1">Category</label>
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php foreach (expenseCategories() as $cat): ?>
                    <option value="<?= h($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= h($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-3">
            <label class="form-label small mb-1">From</label>
            <input type="date" name="from" class="form-control" value="<?= h($from) ?>">
        </div>
        <div class="col-sm-3">
            <label class="form-label small mb-1">To</label>
            <input type="date" name="to" class="form-control" value="<?= h($to) ?>">
        </div>
        <div class="col-sm-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary flex-fill">Filter</button>
            <a href="expenses.php" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted"><?= count($expenses) ?> transaction(s)</span>
        <span class="fw-semibold">Total: <?= formatCurrency((float)$filteredTotal) ?></span>
    </div>

    <?php if (empty($expenses)): ?>
        <p class="text-muted mb-0">No expenses match these filters.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expenses as $exp): ?>
                        <tr>
                            <td><?= formatDate($exp['expense_date']) ?></td>
                            <td><span class="badge text-bg-light border"><?= h($exp['category']) ?></span></td>
                            <td><?= h($exp['description']) ?></td>
                            <td class="text-end"><?= formatCurrency((float)$exp['amount']) ?></td>
                            <td class="text-end">
                                <a href="edit_expense.php?id=<?= (int)$exp['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <a href="delete_expense.php?id=<?= (int)$exp['id'] ?>" class="btn btn-sm btn-outline-danger confirm-delete">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
