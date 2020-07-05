<?php

namespace Spreng\orm\entities;

use JsonSerializable;
use Spreng\connection\Connection;
use Spreng\system\utils\FileUtils;

/**
 * RbEntity
 */
abstract class RbEntity implements JsonSerializable
{
    private $instance;
    private $connectionClass;

    public function __construct(Connection $conn, int $id = null, string $sql = '', array $parameters = [])
    {
        $this->connectionClass = get_class($conn);
        $class = strtolower(FileUtils::fileName(get_called_class()));
        if ($id !== null) {
            $sql = "where id = ?";
            $parameters = [$id];
        }
        if ($sql == '') {
            $this->instance = $conn::dispense($class);
        } else {
            $this->instance = $conn::findOne($class, $sql, $parameters);
        }
    }

    public function index(): array
    {
        $all = get_class_methods(get_called_class());
        return array_diff($all, ['index', '__construct', 'getType', 'fetch', 'persist', 'update', 'jsonSerialize']);
    }

    public function getType()
    {
        return get_called_class();
    }

    protected function fetch($arg = null)
    {
        if ($arg !== null) {
            if (isset($this->instance)) {
                $this->instance->{debug_backtrace()[1]['function']} = $arg;
            }
        } else {
            if (isset($this->instance)) {
                return $this->instance->{debug_backtrace()[1]['function']};
            }
        }
    }

    public function persist(): int
    {
        $conn = new $this->connectionClass;
        $id = $conn::store($this->instance);
        return (int) $id;
    }

    public function update()
    {
        $conn = new $this->connectionClass;
        $conn::store($this->instance);
    }

    public function jsonSerialize()
    {
        $serial = [];
        foreach (array_diff(get_class_methods(get_called_class()), ['__construct', 'persist', 'jsonSerialize', 'getType', 'fetch', 'update', 'index']) as $name) {
            $serial[$name] = $this->{$name}();
        }
        return $serial;
    }
}
