<div dir="rtl" lang="fa">

# ماژول اتصال به دیتابیس PDO

یک ماژول امن، شی‌گرا و سبک برای اتصال به دیتابیس در پروژه‌های PHP.  
این کتابخانه با استفاده از `PDO` و `vlucas/phpdotenv` ساخته شده و دو روش استفاده را پشتیبانی می‌کند:

- **متدهای استاتیک** (استفاده سریع و بدون کلاس جداگانه)
- **ارث‌بری** (ایجاد کلاس‌های `Model` برای مدیریت جداول)

---

## ویژگی‌ها

- ✅ اتصال امن با **Prepared Statements** (مقاوم در برابر SQL Injection)
- ✅ مدیریت متغیرهای محیطی با `.env` (مخفی‌سازی اطلاعات حساس)
- ✅ الگوی **Singleton** برای جلوگیری از اتصالات اضافی
- ✅ متدهای کمکی: `select`, `insert`, `update`, `delete`
- ✅ پشتیبانی از تراکنش‌ها: `beginTransaction`, `commit`, `rollBack`
- ✅ قابلیت استفاده به‌عنوان کتابخانه‌ی مجزا
- ✅ مدیریت خطا با `PDOException`

---

## نصب

کافی است از کامپوزر استفاده کنید:

```bash
composer require karan-safaie-qadi/pdo-module
```

---

## پیکربندی

### ۱. ایجاد فایل `.env`

یک فایل `.env` در **ریشه‌ی پروژه** (هم‌سطح `vendor/`) ایجاد کنید:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
DB_CHARSET=utf8mb4
DB_DRIVER=mysql
```

> **نکته امنیتی:** این فایل را به `.gitignore` اضافه کنید.

### ۲. تنظیم مسیر ریشه

چون کتابخانه در `vendor/` قرار می‌گیرد، مسیر ریشه را یک بار در ابتدای پروژه تنظیم کنید:

```php
require_once __DIR__ . '/vendor/autoload.php';

use Database\Database;

Database::setRootPath(__DIR__);
```

---

## نحوه‌ی استفاده

### روش اول: استفاده‌ی مستقیم (استاتیک)

```php
use Database\Database;

// دریافت تمام رکوردها
$users = Database::select('SELECT * FROM users WHERE age > ?', [18]);

// دریافت یک رکورد
$user = Database::selectOne('SELECT * FROM users WHERE id = ?', [1]);

// درج رکورد جدید
$newId = Database::insert('users', [
    'name' => 'علی رضایی',
    'email' => 'ali@example.com',
    'age' => 25
]);

// بروزرسانی
Database::update('users', ['age' => 26], ['id' => $newId]);

// حذف
Database::delete('users', ['id' => $newId]);

// کوئری دلخواه
$result = Database::execute('SELECT * FROM products WHERE status = ?', ['active']);
```

### روش دوم: استفاده از مدل‌ها (ارث‌بری)

#### ایجاد کلاس مدل

```php
namespace YourProject\Models;

use Models\Model;

class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';
    
    // متد اختصاصی
    public static function findByEmail(string $email): ?array
    {
        return self::selectOne('SELECT * FROM users WHERE email = ?', [$email]);
    }
}
```

#### استفاده از مدل

```php
use YourProject\Models\User;

$allUsers = User::all();
$user = User::find(1);
$newId = User::create(['name' => 'مریم', 'email' => 'maryam@example.com']);
User::updateRecord($newId, ['status' => 'inactive']);
User::deleteRecord($newId);
$user = User::findByEmail('maryam@example.com');
```

---

## تراکنش‌ها

```php
use Database\Database;

try {
    Database::beginTransaction();
    Database::insert('orders', ['user_id' => 1, 'total' => 100]);
    Database::update('users', ['balance' => 900], ['id' => 1]);
    Database::commit();
    echo "تراکنش موفق";
} catch (\PDOException $e) {
    Database::rollBack();
    echo "خطا: " . $e->getMessage();
}
```

---

## مدیریت خطاها

```php
try {
    $users = Database::select('SELECT * FROM non_existent_table');
} catch (\PDOException $e) {
    echo "خطای دیتابیس: " . $e->getMessage();
} catch (\Exception $e) {
    echo "خطای عمومی: " . $e->getMessage();
}
```

بررسی وضعیت اتصال:

```php
if (Database::isConnected()) {
    echo "اتصال برقرار است.";
} else {
    echo "خطا: " . Database::getLastError();
}
```

---

## نکات امنیتی

- تمام متدها از Prepared Statements استفاده می‌کنند.
- تنظیمات امن PDO به‌صورت پیش‌فرض فعال است.

---

## مجوز

این پروژه تحت مجوز **MIT** منتشر شده است.

---

## ارتباط با توسعه‌دهنده

- **گیت‌هاب:** [Karan-Safaie-Qadi/PDO-Connection-Module](https://github.com/Karan-Safaie-Qadi/PDO-Connection-Module)
- **پکیج در Packagist:** [karan-safaie-qadi/pdo-module](https://packagist.org/packages/karan-safaie-qadi/pdo-module)

</div>

---

<hr>

---

<div dir="ltr" lang="en">

# PDO Database Connection Module

A secure, object-oriented, and lightweight database connection module for PHP projects.  
This library uses `PDO` and `vlucas/phpdotenv` and supports two usage methods:

- **Static methods** (quick and without separate classes)
- **Inheritance** (creating `Model` classes for table management)

---

## Features

- ✅ Secure connection with **Prepared Statements** (SQL Injection resistant)
- ✅ Environment variable management with `.env` (hiding sensitive info)
- ✅ **Singleton** pattern to prevent multiple connections
- ✅ Helper methods: `select`, `insert`, `update`, `delete`
- ✅ Transaction support: `beginTransaction`, `commit`, `rollBack`
- ✅ Can be used as a standalone library
- ✅ Error handling with `PDOException`

---

## Installation

Use Composer:

```bash
composer require karan-safaie-qadi/pdo-module
```

---

## Configuration

### 1. Create `.env` file

Create a `.env` file in the **project root** (same level as `vendor/`):

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
DB_CHARSET=utf8mb4
DB_DRIVER=mysql
```

> **Security note:** Add this file to `.gitignore`.

### 2. Set Root Path

Since the library resides in `vendor/`, set the root path once at the project bootstrap:

```php
require_once __DIR__ . '/vendor/autoload.php';

use Database\Database;

Database::setRootPath(__DIR__);
```

---

## Usage

### Method 1: Direct (Static) Usage

```php
use Database\Database;

// Get all records
$users = Database::select('SELECT * FROM users WHERE age > ?', [18]);

// Get one record
$user = Database::selectOne('SELECT * FROM users WHERE id = ?', [1]);

// Insert new record
$newId = Database::insert('users', [
    'name' => 'Ali Rezaei',
    'email' => 'ali@example.com',
    'age' => 25
]);

// Update
Database::update('users', ['age' => 26], ['id' => $newId]);

// Delete
Database::delete('users', ['id' => $newId]);

// Custom query
$result = Database::execute('SELECT * FROM products WHERE status = ?', ['active']);
```

### Method 2: Using Models (Inheritance)

#### Create a Model Class

```php
namespace YourProject\Models;

use Models\Model;

class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';
    
    // Custom method
    public static function findByEmail(string $email): ?array
    {
        return self::selectOne('SELECT * FROM users WHERE email = ?', [$email]);
    }
}
```

#### Using the Model

```php
use YourProject\Models\User;

$allUsers = User::all();
$user = User::find(1);
$newId = User::create(['name' => 'Maryam', 'email' => 'maryam@example.com']);
User::updateRecord($newId, ['status' => 'inactive']);
User::deleteRecord($newId);
$user = User::findByEmail('maryam@example.com');
```

---

## Transactions

```php
use Database\Database;

try {
    Database::beginTransaction();
    Database::insert('orders', ['user_id' => 1, 'total' => 100]);
    Database::update('users', ['balance' => 900], ['id' => 1]);
    Database::commit();
    echo "Transaction successful";
} catch (\PDOException $e) {
    Database::rollBack();
    echo "Error: " . $e->getMessage();
}
```

---

## Error Handling

```php
try {
    $users = Database::select('SELECT * FROM non_existent_table');
} catch (\PDOException $e) {
    echo "Database error: " . $e->getMessage();
} catch (\Exception $e) {
    echo "General error: " . $e->getMessage();
}
```

Check connection status:

```php
if (Database::isConnected()) {
    echo "Connected.";
} else {
    echo "Error: " . Database::getLastError();
}
```

---

## Security Notes

- All methods use Prepared Statements.
- Secure PDO settings are enabled by default.

---

## License

This project is released under the **MIT** license.

---

## Contact

- **GitHub:** [Karan-Safaie-Qadi/PDO-Connection-Module](https://github.com/Karan-Safaie-Qadi/PDO-Connection-Module)
- **Packagist:** [karan-safaie-qadi/pdo-module](https://packagist.org/packages/karan-safaie-qadi/pdo-module)

</div>
```
