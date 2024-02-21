<?php

declare(strict_types=1);

require_once './utils/parse-json.php';
require_once './utils/safe-db.php';

final class Database
{

    private static Database|null $instance = null;
    private PDO $conn;


    private function __construct()
    {
        safe_db_config_init('./db/db_config.json');

        [
            "host" => $host,
            "user" => $user,
            "password" => $password,
            "db_name" => $db_name
        ] = parse_json('./db/db_config.json', 'Файл конфигурации БД отсутствует!');

        try {
            $dsn = "mysql:host=$host;dbname=$db_name";
            $this->conn = new PDO( $dsn, $user, $password);
    
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            print "Connection failed: " . $e->getMessage();
        }

    }

    public static function getDBInstance(): ?Database
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->conn;
    }


}