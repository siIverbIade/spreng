<?php

declare(strict_types=1);

namespace Spreng\connection;

require 'rb.php';

use R;
use PDO;
use PDOException;
use Spreng\config\GlobalConfig;
use Spreng\system\files\CSV;

/**
 * Connection
 */
class Connection extends R
{
    private $connections;
    private $selectedDB;

    public function __construct(string $currentDB, bool $autoload = true)
    {
        $config = GlobalConfig::getConnectionConfig()->getConfig();
        $this->connections = $config;
        foreach ($config as $db => $param) {
            if (!self::testConnection()) self::addDatabase($db, "mysql:host=" . $param['url'] . ":" . $param['port'] . ";dbname=" . $param['database'], $param['user'], $param['password']);
        }
        if ($autoload) {
            $this->selectedDB = $currentDB;
            $this->regenerate();
            $this->pickDatabase($currentDB);
        }
    }

    public function getUrl(): string
    {
        return $this->connections[$this->selectedDB]['url'];
    }

    public function getPort(): string
    {
        return $this->connections[$this->selectedDB]['port'];
    }

    public function getDatabase(): string
    {
        return $this->connections[$this->selectedDB]['database'];
    }

    public function getUser(): string
    {
        return $this->connections[$this->selectedDB]['user'];
    }

    public function getPassword(): string
    {
        return $this->connections[$this->selectedDB]['password'];
    }

    public function allowRegenerate(): bool
    {
        return $this->connections[$this->selectedDB]['regenerate'];
    }

    public function pickDatabase($key)
    {
        $this->selectedDB = $key;
        self::selectDatabase($key, $force = FALSE);
    }

    public function getConfig(string $db): array
    {
        return $this->connections[$db];
    }

    public function regenerate()
    {
        if ($this->allowRegenerate()) $this->new();
    }

    protected function new()
    {
        $url = $this->getUrl() . ":" . $this->getPort();
        $db = $this->getDatabase();
        $user = $this->getUser();
        $password = $this->getPassword();
        try {
            $dbh = new PDO("mysql:host=$url", $user, $password);

            $dbh->exec("CREATE DATABASE IF NOT EXISTS `$db`;
                CREATE USER IF NOT EXISTS'$user'@'$url' IDENTIFIED BY '$password';
                GRANT ALL ON `$db`.* TO '$user'@'$url';
                FLUSH PRIVILEGES;");
        } catch (PDOException $e) {
        }
        $dbh = null;
    }

    public function getConnectionLog(): string
    {
        $currentDB = $this->selectedDB;
        try {
            $db = new PDO("mysql:host=" . $this->connections[$currentDB]['url'] . ":" . $this->connections[$currentDB]['port'] . ";dbname=" . $this->connections[$currentDB]['database'], $this->connections[$currentDB]['user'], $this->connections[$currentDB]['password']);
            $return = "OK";
        } catch (PDOException $e) {
            $return = $e->getmessage();
        }
        return $return;
    }

    public static function createTable(string $table, array $header = [])
    {
        $fields = implode(', ', array_map(function ($h) {
            return "$h VARCHAR(30) NOT NULL";
        }, $header));
        self::exec("DROP TABLE IF EXISTS $table;CREATE TABLE $table(
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            $fields)");
    }

    public function importCSV(string $csvPath, string $table, array $header = [])
    {
        if ($header == []) {
            $csv = new CSV($csvPath, 'municipio', false);
            $header = $csv->getHeader();
        }

        self::createTable($table, $header);

        $headers = implode(', ', $header);
        $currentDB = $this->selectedDB;
        $conn = new PDO("mysql:host=" . $this->connections[$currentDB]['url'] . ":" . $this->connections[$currentDB]['port'] . ";dbname=" . $this->connections[$currentDB]['database'], $this->connections[$currentDB]['user'], $this->connections[$currentDB]['password'], array(
            PDO::MYSQL_ATTR_LOCAL_INFILE => true,
        ));

        $conn->exec("LOAD DATA LOCAL INFILE 'http://localhost/$csvPath' 
            INTO TABLE $table 
            FIELDS TERMINATED BY ';' 
            LINES TERMINATED BY '\\n' 
            IGNORE 1 LINES 
            ($headers)");
    }

    public static function persist($obj)
    {
        self::store(self::dispense($obj)); // persiste a entidade com o RedBean
    }

    public static function persistAll(array $objArray)
    {
        foreach ($objArray as $obj) {
            self::persist($obj);
        }
    }

    public function persistIndex($indexValue, string $indexName, $obj)
    {
        $load = self::findOne($obj['_type'], "$indexName = ?", [$indexValue]);
        if (is_null($load)) {
            self::persist($obj);
            return $obj;
        } else {

            foreach ($obj as $fld => $val) {
                if ($fld !== "_type") $load[$fld] = $val;
            }
            self::store($load);
            return $load;
        }
    }
}
