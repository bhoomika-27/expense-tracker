// Confirm before deleting an expense.
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.confirm-delete').forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (!confirm('Delete this expense? This cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});
