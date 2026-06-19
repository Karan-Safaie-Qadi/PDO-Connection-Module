<?php

namespace Models;

use Database\Database;

abstract class Model extends Database
{
    protected string $table;
    protected string $primaryKey = 'id';

    public static function find(int $id): ?array
    {
        $instance = new static();
        $table = $instance->table;
        $pk = $instance->primaryKey;
        return self::selectOne("SELECT * FROM `$table` WHERE `$pk` = ?", [$id]);
    }

    public static function all(): array
    {
        $table = (new static())->table;
        return self::select("SELECT * FROM `$table`");
    }

    public static function create(array $data): int|string
    {
        $table = (new static())->table;
        return self::insert($table, $data);
    }

    public static function updateRecord(int $id, array $data): int
    {
        $instance = new static();
        $table = $instance->table;
        $pk = $instance->primaryKey;
        return self::update($table, $data, [$pk => $id]);
    }

    public static function deleteRecord(int $id): int
    {
        $instance = new static();
        $table = $instance->table;
        $pk = $instance->primaryKey;
        return self::delete($table, [$pk => $id]);
    }
}