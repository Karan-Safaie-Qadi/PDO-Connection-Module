<?php

namespace Database;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database
{
    private static ?PDO $instance = null;
    private static array $config = [];
    private static bool $isConnected = false;
    private static ?string $lastError = null;

    /** @var string|null مسیر ریشه‌ی پروژه (برای پیدا کردن .env) */
    private static ?string $rootPath = null;

    private static array $defaultPdoOptions = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false,
    ];

    /**
     * تنظیم مسیر ریشه‌ی پروژه (اجباری برای کتابخانه‌های مجزا)
     * 
     * @param string $rootPath مسیر کامل ریشه‌ی پروژه (معمولاً __DIR__ در بوت‌استرپ)
     */
    public static function setRootPath(string $rootPath): void
    {
        self::$rootPath = rtrim($rootPath, '/\\');
    }

    private static function loadEnvironment(): void
    {
        if (!empty(self::$config))
            return;

        // تعیین مسیر ریشه
        if (self::$rootPath !== null) {
            // حالت کتابخانه‌ی مجزا (مسیر توسط کاربر تنظیم شده)
            $projectRoot = self::$rootPath;
        } else {
            // حالت توسعه‌ی محلی (برای زمانی که خود ماژول مستقل است)
            $projectRoot = dirname(__DIR__, 2);
        }

        if (!file_exists($projectRoot . '/.env')) {
            throw new \RuntimeException(
                'فایل .env در مسیر ' . $projectRoot . ' پیدا نشد. ' .
                'لطفاً با متد Database::setRootPath(__DIR__) مسیر ریشه را تنظیم کنید.'
            );
        }

        $dotenv = Dotenv::createImmutable($projectRoot);
        $dotenv->load();

        self::$config = [
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'port' => $_ENV['DB_PORT'] ?? '3306',
            'name' => $_ENV['DB_NAME'] ?? '',
            'username' => $_ENV['DB_USERNAME'] ?? '',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
            'driver' => $_ENV['DB_DRIVER'] ?? 'mysql',
        ];

        if (empty(self::$config['name']) || empty(self::$config['username'])) {
            throw new \RuntimeException('اطلاعات دیتابیس در فایل .env تنظیم نشده است.');
        }
    }

    public static function getConnection(): PDO
    {
        if (self::$instance !== null && self::$isConnected) {
            return self::$instance;
        }

        self::loadEnvironment();

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            self::$config['driver'],
            self::$config['host'],
            self::$config['port'],
            self::$config['name'],
            self::$config['charset']
        );

        try {
            self::$instance = new PDO(
                $dsn,
                self::$config['username'],
                self::$config['password'],
                self::$defaultPdoOptions
            );
            self::$isConnected = true;
            self::$lastError = null;
            return self::$instance;
        } catch (PDOException $e) {
            self::$isConnected = false;
            self::$lastError = $e->getMessage();
            throw $e;
        }
    }

    public static function closeConnection(): void
    {
        self::$instance = null;
        self::$isConnected = false;
    }

    public static function isConnected(): bool
    {
        return self::$isConnected && self::$instance !== null;
    }

    public static function getLastError(): ?string
    {
        return self::$lastError;
    }

    // ==========================================
    // متدهای کمکی برای اجرای کوئری
    // ==========================================

    public static function select(string $sql, array $params = []): array
    {
        $stmt = self::execute($sql, $params);
        return $stmt->fetchAll();
    }

    public static function selectOne(string $sql, array $params = []): ?array
    {
        $stmt = self::execute($sql, $params);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    public static function insert(string $table, array $data): int|string
    {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = sprintf(
            'INSERT INTO `%s` (`%s`) VALUES (%s)',
            $table,
            implode('`, `', $fields),
            implode(', ', $placeholders)
        );

        self::execute($sql, array_values($data));
        return self::$instance->lastInsertId();
    }

    public static function update(string $table, array $data, array $where, string $operator = '='): int
    {
        $setParts = array_map(fn($field) => "`$field` = ?", array_keys($data));
        $whereParts = array_map(fn($field) => "`$field` $operator ?", array_keys($where));

        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE %s',
            $table,
            implode(', ', $setParts),
            implode(' AND ', $whereParts)
        );

        $params = array_merge(array_values($data), array_values($where));
        $stmt = self::execute($sql, $params);
        return $stmt->rowCount();
    }

    public static function delete(string $table, array $where, string $operator = '='): int
    {
        $whereParts = array_map(fn($field) => "`$field` $operator ?", array_keys($where));

        $sql = sprintf(
            'DELETE FROM `%s` WHERE %s',
            $table,
            implode(' AND ', $whereParts)
        );

        $stmt = self::execute($sql, array_values($where));
        return $stmt->rowCount();
    }

    public static function execute(string $sql, array $params = []): \PDOStatement
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    public static function rollBack(): bool
    {
        return self::getConnection()->rollBack();
    }
}