<?php
/**
 * Database configuration.
 *
 * Keep ALL database connection details here so the rest of the app never
 * duplicates connection code. When moving to AWS, only this file changes
 * (Local MySQL -> Amazon RDS MySQL) — nothing else in the application
 * needs to be touched.
 */

// ---- Connection settings -------------------------------------------------
// Local XAMPP defaults shown below. When you move to RDS, update DB_HOST
// to your RDS endpoint (and DB_USER / DB_PASS to your RDS credentials).
define('DB_HOST', 'localhost');   // e.g. 'mydb.xxxxxxx.ap-south-1.rds.amazonaws.com' on RDS
define('DB_PORT', '3306');
define('DB_NAME', 'expense_tracker');
define('DB_USER', 'root');        // XAMPP default user
define('DB_PASS', '');            // XAMPP default password (empty)

/**
 * Returns a shared PDO connection using prepared-statement-friendly defaults.
 * PDO is used throughout the app so all queries can use bound parameters
 * instead of concatenating user input into SQL.
 */
function getDbConnection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Never leak connection details (host/user/pass) to the browser.
            error_log('Database connection failed: ' . $e->getMessage());
            die('Database connection failed. Please try again later.');
        }
    }

    return $pdo;
}
