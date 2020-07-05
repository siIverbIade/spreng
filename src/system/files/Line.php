<?php

declare(strict_types=1);

namespace Spreng\system\files;

use Exception;

/**
 * Class ModeloLinha
 * Trabalha os atributos da linha, relacionando com uma palavra-chave com algumas manipulações úteis para desafogar
 * ModeloDocumento
 */
class Line
{
    private $line; // texto da linha em si
    private $keyword; // palavra chave a ser trabalhada
    private $cleanLine; // linha com a palavra chave removida
    private $pos; // posição da string $line onde a palavra está
    private $arrayLine; // linha quebrada em campos através de um caractere separador

    public function __construct(string $lin, string $key = "", string $brkhr) // carrega a linha com o argumento passado no construtor
    {
        $this->line = $lin;
        if ($key == "") $key = $lin;
        $this->keyword = $key;

        if ($this->achou()) {
            $this->cleanLine = trim(str_replace($this->keyword, '', $this->line)); // método para pegar a linha com a palavra chave removida
            $this->arrayLine = explode($brkhr, trim($this->line)); // vetoriza a linha utilizando caractere $brkhr como separador
        } else {
            $this->cleanLine = "";
            $this->arrayLine = array();
        }
    }

    public function setKeyword(string $keyWord)
    {
        $this->keyword = $keyWord;
    }

    public function lookup()
    {
        try {
            $this->pos = strpos($this->line, $this->keyword);
        } catch (Exception $e) {
            echo "KEYWORD NOT FOUND = " . $this->keyword . "\n";
        }
    }

    public function text(): string
    {
        return $this->line;
    }

    public function posicao()
    {
        return $this->pos;
    }

    public function linhaSemPChave(): string
    {
        return $this->cleanLine;
    }

    public function getVetorizada(): array  // esta função foi definida pública para ser utilizada em MotorBusca
    {
        return $this->arrayLine;
    }

    public function valor(): string
    {
        foreach ($this->getVetorizada() as $index => $text) { //percorre a linha quebrada
            if ($this->keyword == $text) { // queremos a posição $indice na qual $texto == $palavraChave
                $valor = $this->getVetorizada()[$index + 1]; // $linhaQuebrada[$indice + 1] é o texto que segue a palavra chave
                break;
            }
            $valor = "N/D";
        }
        return $valor;
    }

    public function achou(): bool
    {
        $this::lookup();
        if ($this->pos !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function unica(): bool
    {
        return (count($this->arrayLine) == 1 && ($this->pos == 0));
    }

    public function vazia(): bool
    {
        if ($this->cleanLine == '') {
            return true;
        } else {
            return false;
        } //erro na validação deste método estava causando desvio para o caso (2) em que na verdade cleanLine não estava vazio
    }
}
