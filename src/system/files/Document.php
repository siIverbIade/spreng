<?php

declare(strict_types=1);

namespace Spreng\system\files;

/**
 * Class ModeloDocumento:
 * Manupula todos atributos necessários ao documento como texto, texto da linha, número de páginas, navegação, bookmarks entre outros
 */
class Document
{
    private $strContent = "";
    private $pageKey = "	";
    private $pointer = 0; // coloca o leitor na primeira linha do arquivo
    private $arrayFile = [];
    private $pgCur = [];
    private $linesize = [];
    public $bookmark = [];
    private $excludedBookmarks = [];

    public function __construct(string $strContent, string $pgKey = "Página", bool $autoload = false)
    {
        $this->pageKey = $pgKey; // atribui o curinga separador de páginas passado em $pgKey
        $this->strContent = $strContent; // pega o conteúdo do texto e remove lixo como espaços em branco e linhas somente com pontos.
        if ($autoload) $this->load();
    }

    public function load()
    {
        //inicializa{
        $sum = 0;
        $this->pgCur = array();
        $this->bookmark = array();
        $this->linesize = array();
        $this->arrayFile = explode("\n", $this->strContent); // quebra o texto em um vetor por linha
        //}
        foreach ($this->arrayFile as $index => $line) {
            array_push($this->linesize, $sum);
            $lineObj = new Line($line, $this->pageKey, "	");
            if ($lineObj->achou()) {
                array_push($this->pgCur, $index);
            }
            $sum += strlen($line) + 1;
        }
    }

    public function getBookMark(): string
    {
        if (array_key_exists($this->pointer, $this->bookmark)) {
            return $this->bookmark[$this->pointer];
        } else {
            return "?";
        }
    }

    public function setBookMark(string $value)
    {
        $this->bookmark[$this->pointer] = $value;
    }

    public function cleanAllBookMarks()
    {
        $this->bookmark = array();
    }

    public function hasBookMarks(): bool
    {
        $num_marks = func_num_args();
        $args = func_get_args();
        $return = true;
        if ($num_marks == 0) {
            return $this->getBookMark() !== "?";
        } else {
            foreach ($args as $arg) {
                if (substr($arg, 0, 1) == "/") {
                    $return = $return && ("/" . $this->getBookMark() !== $arg);
                } else {
                    $return = $return || ($this->getBookMark() == $arg);
                }
            }
        }
        return $return;
    }

    public function getExcluded(): array
    {
        return $this->excludedBookmarks;
    }

    public function setExcluded(array $array)
    {
        $this->excludedBookmarks = $array;
    }

    public function checkFilter(array $array): bool
    {
        $return = true;
        if (array_key_exists("_filter", $array)) {
            if (array_key_exists("ano", $array["_filter"])) {
                if (!in_array($this->anoApuracao, $array["_filter"]["ano"])) {
                    $return = false;
                }
            }
            if (array_key_exists("regime", $array["_filter"])) {
                if ($this->regimeApuracao !== $array["_filter"]["regime"]) {
                    $return = false;
                }
            }
        }
        return $return;
    }

    public function fullText(): string
    {
        return $this->strContent;
    }

    public function findOcurr(string $findme): int // encontra a primeira ocorrência de $findme após a linha atual
    {
        $found = strpos($this->strContent, $findme, $this->getUsedCharsFromLineBeggining($this->pointer));
        if (!$found == false) {
            return $this->getLineFromUsedChars($found + strlen($findme));
        } else {
            return -1;
        }
    }

    public function line(): string
    {
        if (!$this->end()) {
            return $this->arrayFile[$this->pointer];
        } else {
            return "";
        }
    }

    public function begin(): bool
    {
        return ($this->pointer == 0);
    }

    public function end(): bool
    {
        return ($this->pointer >= count($this->arrayFile));
    }

    public function next(array $additionalSkip = [], bool $skipExcluded = true)
    {
        $this->pointer++;
        if ($skipExcluded) {
            while (in_array($this->getBookMark(), array_merge($this->excludedBookmarks, $additionalSkip))) {
                $this->pointer++;
            }
        }
    }

    public function previous(array $additionalSkip = [], bool $skipExcluded = true)
    {
        $this->pointer--;
        if ($skipExcluded) {
            while (in_array($this->getBookMark(), array_merge($this->excludedBookmarks, $additionalSkip))) {
                $this->pointer--;
            }
        }
    }

    public function goto(int $num)
    {
        if ($num >= 0 && $num <= count($this->arrayFile)) {
            $this->pointer = $num;
        }
    }

    public function pos(): int
    {
        return $this->pointer;
    }

    public function currPg(): int
    {
        $ret = 0;
        foreach ($this->pgCur as $index => $value) {
            if ($this->pointer > $value) {
                $ret = $index;
            }
        }
        return $ret + 1;
    }

    public function pages(): int
    {
        return count($this->pgCur);
    }

    public function count(): int
    {
        return count($this->arrayFile);
    }

    public function getStartOfPgs(): array
    {
        return $this->pgCur;
    }

    public function getTextLine(string $keyWord, string $lnbrk = "	"): Line
    {
        return new Line($this->line(), $keyWord, $lnbrk);
    }

    private function getUsedCharsFromLineBeggining(int $lineNumber): int
    {
        return $this->linesize[$lineNumber];
    }

    private function getLineFromUsedChars(int $charNumber): int
    {
        foreach ($this->linesize as $index => $value) {
            if ($value > $charNumber) {
                return $index - 1;
            }
        }
    }
}
