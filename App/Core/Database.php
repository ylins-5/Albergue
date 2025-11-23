<?php
namespace App\Core;

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct(array $config)
    {
        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['db_charset']}";
        try {
            $this->pdo = new \PDO($dsn, $config['db_user'], $config['db_pass'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao conectar no banco', 'msg' => $e->getMessage()]);
            exit;
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/config.php';
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
