<?php
// config/database.php

class Database {
    private static $host = 'localhost';
    private static $db_name = 'pawsome_connections';
    private static $username = 'root';
    private static $password = '';
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$db_name,
                    self::$username,
                    self::$password
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$conn->exec("set names utf8");
            } catch(PDOException $exception) {
                throw new Exception("Connection error: " . $exception->getMessage());
            }
        }
        return self::$conn;
    }
}
?>
