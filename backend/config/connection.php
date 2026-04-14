<?php

/**
 * Memuat file .env (format KEY=VALUE) ke getenv() / $_ENV.
 * - Root project: /.env (satu file dengan Docker Compose & Vite)
 * - Cadangan: /backend/.env
 * Nilai yang sudah disetel lingkungan (mis. dari Docker) tidak ditimpa.
 */
if (!function_exists('tera_load_dotenv')) {
    function tera_load_dotenv(): void
    {
        $paths = [
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env',
            dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env',
        ];
        foreach ($paths as $path) {
            if (!is_readable($path)) {
                continue;
            }
            $lines = file($path, FILE_IGNORE_NEW_LINES);
            if ($lines === false) {
                continue;
            }
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || (isset($line[0]) && $line[0] === '#')) {
                    continue;
                }
                $eq = strpos($line, '=');
                if ($eq === false) {
                    continue;
                }
                $key = trim(substr($line, 0, $eq));
                if ($key === '') {
                    continue;
                }
                $value = trim(substr($line, $eq + 1));
                $len = strlen($value);
                if ($len >= 2 && (($value[0] === '"' && $value[$len - 1] === '"') || ($value[0] === "'" && $value[$len - 1] === "'"))) {
                    $value = substr($value, 1, -1);
                }
                if (getenv($key) !== false) {
                    continue;
                }
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
            }
            break;
        }
    }
}

tera_load_dotenv();

if (!function_exists('tera_env_first')) {
    function tera_env_first(array $keys, ?string $default = null): ?string
    {
        foreach ($keys as $key) {
            $value = getenv($key);
            if ($value !== false && $value !== '') {
                return $value;
            }
        }
        return $default;
    }
}

class Database {
    private static $instance = null;
    private $connection;
    
    private $host;
    private $port;
    private $dbname;
    private $username;
    private $password;

    private function __construct() {
        // Prioritas env:
        // 1) DB_* (lokal/VPS native)
        // 2) MYSQL* (Railway MySQL variables)
        $this->host = tera_env_first(['DB_HOST', 'MYSQLHOST'], 'localhost');
        $this->port = tera_env_first(['DB_PORT', 'MYSQLPORT'], '3306');
        $this->dbname = tera_env_first(['DB_NAME', 'MYSQLDATABASE'], 'terassamarinda');
        $this->username = tera_env_first(['DB_USER', 'MYSQLUSER'], 'root');
        $this->password = tera_env_first(['DB_PASSWORD', 'MYSQLPASSWORD'], '');

        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
        
        try {
            $this->connection = new PDO($dsn, $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    
    public function select($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function selectOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        
        return $this->connection->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach (array_keys($data) as $key) {
            $set[] = "{$key} = :{$key}";
        }
        $set = implode(', ', $set);
        
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        $this->query($sql, array_merge($data, $whereParams));
        
        return true;
    }
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $this->query($sql, $params);
        return true;
    }
    
    private function __clone() {}
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}