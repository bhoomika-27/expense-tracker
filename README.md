# Personal Expense Tracker

A simple PHP + MySQL expense tracker, built to run locally on XAMPP first,
then later deploy as the application layer of an AWS 3-tier architecture
(ALB → EC2 → RDS MySQL).

## Tech stack
HTML5, CSS3, Bootstrap 5, vanilla JavaScript, PHP 8+, MySQL, Apache.

## Project structure
```
expense-tracker/
├── config/database.php     # All DB connection details live here
├── includes/                # header, footer, auth, shared functions
├── assets/                  # css, js
├── index.php, login.php, register.php
├── dashboard.php, expenses.php
├── add_expense.php, edit_expense.php, delete_expense.php
├── logout.php
└── database.sql             # schema + sample data
```

## Setup on XAMPP

1. **Install/start Apache and MySQL** in the XAMPP Control Panel.
2. **Copy the project folder** into your XAMPP `htdocs` directory, e.g.
   `C:\xampp\htdocs\expense-tracker` (Windows) or `/Applications/XAMPP/htdocs/expense-tracker` (Mac).
3. **Create the database**: open phpMyAdmin (`http://localhost/phpmyadmin`),
   go to the **Import** tab, and import `database.sql`. This creates the
   `expense_tracker` database, the `users` and `expenses` tables, and a
   sample test user with a few sample expenses.
4. **Check `config/database.php`** — the defaults (`localhost`, user `root`,
   empty password) match a standard XAMPP install, so you usually don't
   need to change anything locally.
5. **Open the app**: go to `http://localhost/expense-tracker/` in your browser.
6. **Register a new account** (recommended) or log in with the sample test
   user described below.
7. Add, edit, filter, and delete expenses from the dashboard.

## Test user

`database.sql` includes one ready-to-use sample account:

- **Email:** `test@example.com`
- **Password:** `Test@1234`

Its password is stored as a real bcrypt hash (not plaintext), so it works
immediately with `password_verify()` after you import `database.sql`. You
can also just register your own account through the Register page — it
runs `password_hash()` for you the same way.

## Security notes
- Passwords are hashed with `password_hash()` / `password_verify()` — never stored in plaintext.
- All database queries use PDO prepared statements (no string concatenation of user input into SQL).
- Every expense query/update/delete is scoped by `user_id`, so a user can never view or modify another user's expenses by changing an `id` in the URL.
- Authenticated pages call `requireLogin()`, which redirects anonymous visitors to `login.php`.
- All user-supplied output is escaped with `htmlspecialchars()` via the `h()` helper before being echoed into HTML.

## Moving to AWS later
When you're ready to deploy as a 3-tier architecture, only `config/database.php`
needs to change — update `DB_HOST`, `DB_USER`, and `DB_PASS` to point at your
Amazon RDS MySQL instance instead of local MySQL. No application code changes
should be needed.
