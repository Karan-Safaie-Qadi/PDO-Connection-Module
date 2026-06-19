<?php

require_once __DIR__ . '/vendor/autoload.php';

use Database\Database;

// ✅ مرحله‌ی ۱: تنظیم مسیر ریشه (فقط یک بار در ابتدای پروژه)
Database::setRootPath(__DIR__);

// ✅ مرحله‌ی ۲: حالا از اتصال استفاده کنید
try {
    $users = Database::select('SELECT * FROM users');
    print_r($users);
} catch (\PDOException $e) {
    echo 'خطا: ' . $e->getMessage();
}