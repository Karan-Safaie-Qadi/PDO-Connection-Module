<?php

require_once __DIR__ . '/vendor/autoload.php';

use Database\Database;

echo "=== تست ماژول اتصال به دیتابیس ===\n\n";

try {
    // برقراری اتصال
    $pdo = Database::getConnection();
    echo "✅ اتصال به دیتابیس با موفقیت برقرار شد.\n";
    
    // اجرای یک کوئری ساده برای تست (بدون وابستگی به جدول)
    $result = Database::select("SELECT 1 as test");
    echo "✅ کوئری تست با موفقیت اجرا شد.\n";
    
    // دریافت اطلاعات نسخه‌ی دیتابیس (اختیاری)
    $version = Database::selectOne("SELECT VERSION() as db_version");
    if ($version) {
        echo "✅ نسخه‌ی دیتابیس: " . $version['db_version'] . "\n";
    }
    
    echo "✅ وضعیت اتصال: " . (Database::isConnected() ? 'متصل' : 'قطع') . "\n";
    
    // بستن اتصال (اختیاری)
    Database::closeConnection();
    echo "✅ اتصال بسته شد.\n";
    
} catch (\PDOException $e) {
    echo "❌ خطا در اتصال یا اجرای کوئری: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "❌ خطای عمومی: " . $e->getMessage() . "\n";
}

echo "\n✅ ماژول با موفقیت اجرا شد.\n";