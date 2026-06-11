# Loan Management System – Setup Instructions

## Requirements
- PHP 8.2+
- Composer
- MySQL 5.7+ or MariaDB 10.4+
- Node.js 18+ (optional, for asset builds)

## Installation Steps

**1. Clone / extract the project into your web root.**

**2. Install PHP dependencies:**
```
composer install
```

**3. Copy the environment file:**
```
cp .env.example .env
```

**4. Generate application key:**
```
php artisan key:generate
```

**5. Create the MySQL database:**
```sql
CREATE DATABASE loan_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**6. Update `.env` with your database credentials:**
```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=loan_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

**7a. Option A – Run migrations and seed (fresh start):**
```
php artisan migrate --seed
```

**7b. Option B – Import the SQL dump directly:**
```
mysql -u root -p loan_management < loan_management.sql
```

**8. Start the development server:**
```
php artisan serve
```

Open http://127.0.0.1:8000 in your browser.

---

## Default Login Credentials

| Role     | Email             | Password  |
|----------|-------------------|-----------|
| Admin    | admin@loan.com    | admin123  |
| Customer | abhi979599@gmail.com | Abhi@123  |

---

## Feature Summary

**Customer:**
- Register & login
- Apply for a loan (amount, tenure, purpose)
- View all loan applications and their status
- View repayment schedule for approved loans
- Mark installment payments

**Admin:**
- View all loan applications with status filter & search
- Approve a loan (set interest rate, auto-generates EMI + repayment schedule)
- Reject a loan with a reason
- View repayment tracking per loan

---