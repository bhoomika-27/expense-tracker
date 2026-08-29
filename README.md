# Personal Expense Tracker — Deployed on AWS (3-Tier Architecture)

A simple, functional Personal Expense Tracker built with PHP and MySQL, deployed on AWS using a proper 3-tier architecture: **Application Load Balancer → EC2 → RDS MySQL**, all inside a custom VPC with public/private subnet isolation.

This project was built as a learning exercise during [AWS re/Start](https://aws.amazon.com/training/restart/) — the goal was to keep the application layer simple and beginner-friendly so the real focus could stay on the AWS infrastructure: networking, security boundaries, and deployment.

## Architecture


```
Internet
   │
   ▼
Application Load Balancer  (public subnets · alb-sg: HTTP from anywhere)
   │
   ▼
EC2 — Apache + PHP          (public subnet · web-sg: HTTP only from alb-sg)
   │
   ▼
RDS — MySQL                 (private subnets · db-sg: MySQL only from web-sg)
```

**Why this shape matters:** the browser can only ever reach the load balancer. The load balancer is the only thing `web-sg` trusts, and EC2 is the only thing `db-sg` trusts. Even with the database's hostname in hand, there's no network path to it from outside the VPC — the private subnets simply have no route to the internet. Security here is enforced at the network layer, not just the application layer.

## Tech stack

**Application:** HTML5, CSS3, Bootstrap 5, vanilla JavaScript, PHP 8+, MySQL, Apache
**Infrastructure:** AWS VPC, EC2, RDS (MySQL), Application Load Balancer, Security Groups

Deliberately *not* used: React/Angular/Vue, Node.js, Laravel, Docker, Kubernetes — the goal was to keep the application simple enough to fully understand end-to-end, so all learning effort could go toward the AWS side.

## Features

- User registration, login, logout (PHP sessions)
- Add, edit, delete expenses — each user only ever sees their own
- Filter expenses by category and/or date range
- Dashboard with total spend, current-month spend, transaction count, and a category breakdown
- Passwords hashed with `password_hash()` / `password_verify()` — never stored in plaintext
- All database queries use PDO prepared statements — no raw string concatenation of user input into SQL
- Every expense read/update/delete is scoped by `user_id`, so a user can never touch another user's data by changing an `id` in the URL

## AWS infrastructure, piece by piece

| Component | Purpose |
|---|---|
| **VPC** (`10.0.0.0/16`) | An isolated network boundary for the whole project |
| **Public subnets** (2 AZs) | Host the ALB and EC2 — reachable from the internet via an Internet Gateway |
| **Private subnets** (2 AZs) | Host RDS — no internet route at all |
| **`alb-sg`** | Allows inbound HTTP (80) from anywhere |
| **`web-sg`** | Allows inbound HTTP (80) *only* from `alb-sg`, and SSH (22) from a trusted IP |
| **`db-sg`** | Allows inbound MySQL (3306) *only* from `web-sg` |
| **EC2** | Runs Apache + PHP 8, application code deployed via `git clone` from this repo |
| **RDS (MySQL)** | Managed MySQL instance, schema loaded from `database.sql` |
| **ALB** | Public entry point; forwards HTTP traffic to EC2 via a target group with health checks |

## Local setup (XAMPP)

1. Install/start Apache and MySQL in XAMPP.
2. Copy this repo into your XAMPP `htdocs` directory.
3. Import `database.sql` via phpMyAdmin (creates the database, tables, and a sample test user).
4. Check `config/database.php` — the XAMPP defaults (`localhost`, user `root`, empty password) should work out of the box.
5. Open `http://localhost/expense-tracker/` in your browser.
6. Register an account, or log in with the sample user: `test@example.com` / `Test@1234`.

## AWS deployment (high level)

1. Create a VPC with 2 public + 2 private subnets across 2 Availability Zones, an Internet Gateway, and appropriate route tables.
2. Create `web-sg` and `db-sg`, with `db-sg` sourcing MySQL access from `web-sg` (not an IP range).
3. Launch RDS MySQL in the private subnets, not publicly accessible, secured by `db-sg`.
4. Launch an EC2 instance in a public subnet, secured by `web-sg`; install Apache, PHP, and the MySQL client.
5. Clone this repo onto EC2 into `/var/www/html`, set ownership/permissions for the `apache` user.
6. Update `config/database.php` with the RDS endpoint and credentials (this file is gitignored — see below).
7. Import `database.sql` into RDS.
8. Create a target group + Application Load Balancer in front of EC2, with its own `alb-sg`.
9. Lock `web-sg` to only accept HTTP from `alb-sg`, so EC2 is no longer reachable by its own public IP.

## Security notes

- **Credentials are never committed.** `config/database.php` is listed in `.gitignore`. A previous commit briefly included real RDS credentials during initial development — the RDS master password was rotated immediately after, so that historical commit poses no risk.
- **Defense in depth:** the network layer (VPC subnet routing + security groups) enforces isolation independently of anything the application code does.
- **No public database access, ever** — RDS lives in private subnets with no internet route, regardless of security group rules.

## What I'd add next

- Auto Scaling group behind the ALB (multiple EC2 instances instead of one)
- HTTPS via an ACM certificate on the ALB
- Infrastructure as Code (Terraform/CloudFormation) so this environment is reproducible without manual console steps
- CloudWatch alarms and basic monitoring

## Project structure

```
expense-tracker/
├── config/database.php      # DB connection (gitignored — see Security notes)
├── includes/                 # header, footer, auth, shared functions
├── assets/                   # css, js
├── index.php, login.php, register.php
├── dashboard.php, expenses.php
├── add_expense.php, edit_expense.php, delete_expense.php
├── logout.php
├── docs/architecture.svg
└── database.sql
```

---

Built by Bhoomika Panchal.
