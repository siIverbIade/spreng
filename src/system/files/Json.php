<?php

declare(strict_types=1);

namespace Spreng\system\files;

use Exception;

/**
 * Json
 */
class Json
{
    private $schemaFilePath; // caminho do arquivo .json com o esquema pré definido
    private $schemaJSONstring; // o arquivo já lido em texto puro
    public $schemaJSON; // o objeto array já decodificado do arquivo para permitir navegar pelos campos

    function __construct(string $schemaFilePath, bool $process = false)
    {
        if (!file_exists($schemaFilePath)) throw new Exception('File \'' . $schemaFilePath . '\' not found!');
        $this->setNewSchema($schemaFilePath);
        if ($process) $this->process(); // chama a rotina interna de processamento
    }

    public function process() // lê processa o arquivo
    {
        $this->schemaJSONstring = file_get_contents($this->schemaFilePath);
        $this->schemaJSON = json_decode($this->schemaJSONstring, true); // decodifica a string json do esquema lido acima num M.D.A (Array Multi-Dimensional, devido ao parametro 'true')
    }

    private function setNewSchema(string $schemaJSON)
    {
        $this->schemaFilePath = $schemaJSON;
    }

    public function getSchemaJSON()
    {
        return $this->schemaJSON;
    }

    public function writeSchemaJSON()
    {
        return fwrite(fopen($this->schemaFilePath, "w"), json_encode($this->schemaJSON, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES));
    }

    public function getSchemaString(): string
    {
        return $this->schemaJSONstring;
    }

    public function getValue(array $fields) // retorna o valor de um vetor de campos tipo ["campo_1", "subcampo_1",...,"subcampo_n"]
    {
        $getValue = $this->schemaJSON;
        foreach ($fields as $fld) {
            if (array_key_exists($fld, $getValue)) {
                $getValue = $getValue[$fld];
            }
        }
        return $getValue;
    }
}
