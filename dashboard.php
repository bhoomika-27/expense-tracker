<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';

requireLogin();

$pdo = getDbConnection();
$userId = currentUserId();

// Total expenses (all time)
$stmt = $pdo->prepare('SELECT COALESCE(SUM(amount), 0) AS total, COUNT(*) AS cnt FROM expenses WHERE user_id = ?');
$stmt->execute([$userId]);
$totals = $stmt->fetch();

// Current month expenses
$stmt = $pdo->prepare(
    'SELECT COALESCE(SUM(amount), 0) AS total, COUNT(*) AS cnt
     FROM expenses
     WHERE user_id = ? AND YEAR(expense_date) = YEAR(CURDATE()) AND MONTH(expense_date) = MONTH(CURDATE())'
);
$stmt->execute([$userId]);
$monthTotals = $stmt->fetch();

// Category summary (current month)
$stmt = $pdo->prepare(
    'SELECT category, COALESCE(SUM(amount), 0) AS total
     FROM expenses
     WHERE user_id = ? AND YEAR(expense_date) = YEAR(CURDATE()) AND MONTH(expense_date) = MONTH(CURDATE())
     GROUP BY category
     ORDER BY total DESC'
);
$stmt->execute([$userId]);
$categorySummary = $stmt->fetchAll();
$maxCategoryTotal = 0;
foreach ($categorySummary as $row) {
    $maxCategoryTotal = max($maxCategoryTotal, (float)$row['total']);
}

// Recent expenses
$stmt = $pdo->prepare('SELECT * FROM expenses WHERE user_id = ? ORDER BY expense_date DESC, id DESC LIMIT 5');
$stmt->execute([$userId]);
$recent = $stmt->fetchAll();

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>

<h2 class="mb-4">Dashboard</h2>

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card stat-card p-3">
            <small>Total Expenses</small>
            <h3><?= formatCurrency((float)$totals['total']) ?></h3>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card stat-card p-3">
            <small>This Month</small>
            <h3><?= formatCurrency((float)$monthTotals['total']) ?></h3>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card stat-card p-3">
            <small>Transactions</small>
            <h3><?= (int)$totals['cnt'] ?></h3>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <h5 class="mb-3">Category Summary (This Month)</h5>
            <?php if (empty($categorySummary)): ?>
                <p class="text-muted mb-0">No expenses recorded this month yet.</p>
            <?php else: ?>
                <?php foreach ($categorySummary as $row): ?>
                    <?php $pct = $maxCategoryTotal > 0 ? ((float)$row['total'] / $maxCategoryTotal) * 100 : 0; ?>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span><?= h($row['category']) ?></span>
                            <span><?= formatCurrency((float)$row['total']) ?></span>
                        </div>
                        <div class="category-bar"><span style="width: <?= $pct ?>%"></span></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Recent Expenses</h5>
                <a href="expenses.php" class="small">View all</a>
            </div>
            <?php if (empty($recent)): ?>
                <p class="text-muted mb-0">No expenses yet. <a href="add_expense.php">Add your first one</a>.</p>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($recent as $exp): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <div><?= h($exp['description'] ?: $exp['category']) ?></div>
                                <small class="text-muted"><?= h($exp['category']) ?> · <?= formatDate($exp['expense_date']) ?></small>
                            </div>
                            <span class="fw-semibold"><?= formatCurrency((float)$exp['amount']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
