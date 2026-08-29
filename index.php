<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$pageTitle = 'Welcome';
require __DIR__ . '/includes/header.php';
?>

<div class="p-5 mb-4 bg-white rounded-3 shadow-sm text-center">
    <h1 class="display-6 fw-bold">Take control of your spending</h1>
    <p class="col-lg-8 mx-auto text-muted">
        A simple, no-frills way to track your daily expenses, see where your
        money goes each month, and stay on budget.
    </p>
    <div class="d-flex justify-content-center gap-2 mt-3">
        <a href="register.php" class="btn btn-primary btn-lg">Get Started</a>
        <a href="login.php" class="btn btn-outline-secondary btn-lg">Login</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card p-4 h-100">
            <h5>📊 Track Everything</h5>
            <p class="text-muted mb-0">Log every expense with category, amount, and date.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 h-100">
            <h5>🔍 Filter & Search</h5>
            <p class="text-muted mb-0">Filter by category or date range to see exactly what you spent.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 h-100">
            <h5>📈 Monthly Insights</h5>
            <p class="text-muted mb-0">See totals and category breakdowns at a glance.</p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
