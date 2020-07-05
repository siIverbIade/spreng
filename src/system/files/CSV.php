<?php

namespace Spreng\system\files;

use Spreng\system\log\Logger;

/**
 * CSV
 */
class CSV
{
    private $csv;
    private $type;
    private $handle;

    public function __construct(string $csvPath, string $type, bool $auto = true)
    {
        $this->type = $type;
        if (($this->handle = fopen($csvPath, 'r')) == false) {
            Logger::warning("$csvPath could not be read.");
            return false;
        }
        if ($auto) $this->process();
    }

    public function process()
    {
        $headers = $this->getHeader();
        $this->csv = array();
        array_push($headers, '_type');
        while ($row = fgetcsv($this->handle, 512, ';')) {
            array_push($row, $this->type);
            $this->csv[] = array_combine($headers, $row);
        }

        fclose($this->handle);
        return true;
    }

    public function getHeader(): array
    {
        return fgetcsv($this->handle, 256, ';');
    }

    public function getArray(): array
    {
        return $this->csv;
    }

    public function getColumn(string $colName): array
    {
        $return = [];
        foreach ($this->csv as $line) {
            $return[] = $line[$colName];
        }
        return $return;
    }
}
