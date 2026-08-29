<?php
/**
 * Small shared helper functions used across pages.
 */

/** Escape a string for safe HTML output. */
function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** The fixed list of allowed expense categories. */
function expenseCategories(): array
{
    return ['Food', 'Transport', 'Shopping', 'Bills', 'Education', 'Entertainment', 'Healthcare', 'Other'];
}

/** Format a number as Indian Rupees, e.g. 1234.5 -> "₹1,234.50". */
function formatCurrency(float $amount): string
{
    return '₹' . number_format($amount, 2);
}

/** Format a Y-m-d date as d/m/Y for display. */
function formatDate(string $ymd): string
{
    $dt = DateTime::createFromFormat('Y-m-d', $ymd);
    return $dt ? $dt->format('d/m/Y') : h($ymd);
}

/**
 * Validate expense form input.
 * Returns an array of error messages (empty array = valid).
 */
function validateExpenseInput(string $amount, string $category, string $date): array
{
    $errors = [];

    if ($amount === '' || !is_numeric($amount) || (float)$amount <= 0) {
        $errors[] = 'Please enter a valid amount greater than 0.';
    }

    if (!in_array($category, expenseCategories(), true)) {
        $errors[] = 'Please choose a valid category.';
    }

    $dt = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dt || $dt->format('Y-m-d') !== $date) {
        $errors[] = 'Please enter a valid date.';
    }

    return $errors;
}
