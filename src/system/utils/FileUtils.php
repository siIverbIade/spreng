<?php

declare(strict_types=1);

namespace Spreng\system\utils;

/**
 * FileUtils
 */
class FileUtils
{
    public static function fileName($fullFilePath)
    {
        return pathinfo(str_replace('\\', '/', $fullFilePath), PATHINFO_FILENAME);
    }

    public static function fileExtension($fullFilePath)
    {
        return pathinfo(str_replace('\\', '/', $fullFilePath), PATHINFO_EXTENSION);
    }

    public static function fileBaseName($fullFilePath)
    {
        return pathinfo(str_replace('\\', '/', $fullFilePath), PATHINFO_BASENAME);
    }

    public static function fileDirName($fullFilePath)
    {
        return pathinfo(str_replace('\\', '/', $fullFilePath), PATHINFO_DIRNAME);
    }

    public static function appendTo(string $file, string $content)
    {
        $fp = fopen($file, 'a'); //opens file in append mode  
        fwrite($fp, $content);
        fclose($fp);
    }

    public static function get_parent_script()
    {
        $backtrace = debug_backtrace(
            defined("DEBUG_BACKTRACE_IGNORE_ARGS")
                ? DEBUG_BACKTRACE_IGNORE_ARGS
                : FALSE
        );
        $top_frame = array_pop($backtrace);
        return self::fileName($top_frame['file']);
    }

    public static function isPhpFile(string $phpFileName)
    {
        $split = explode('.', $phpFileName);
        return ($split[count($split) - 1] == 'php');
    }

    public static function dirToArray(string $dir, callable $filter = null): array
    {
        $result = [];
        self::dirToArrayRecursive($dir, $result, $filter);
        return $result;
    }

    private static function dirToArrayRecursive(string $dir, array &$result, callable $filter = null)
    {
        if ($filter == null) {
            $filter = function ($path) {
                return [null, $path];
            };
        }

        $files = scandir($dir);
        foreach ($files as $key => $value) {
            if (!in_array($value, [".", ".."])) {
                $path = $dir . DIRECTORY_SEPARATOR . $value;
                if (is_dir($path)) {
                    self::dirToArrayRecursive($path, $result, $filter);
                } else {
                    $f = $filter($path);
                    if ($f[1] !== null) {
                        $fvalue = $f[1];
                        if ($f[0] !== null) {
                            $result[$f[0]] = $fvalue;
                        } else {
                            $result[] = $fvalue;
                        }
                    }
                }
            }
        }
    }
    public static function delFiles(array $files, callable $filter = null): array
    {
        $return = [];
        foreach ($files as $file) { // iterate files
            if (is_file($file) && ($filter($file) | $filter == null)) {
                array_push($return, $file);
                unlink($file); // delete file
            }
        }
        return $return;
    }

    public static function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            return "$dirPath não é um diretório válido.";
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public static function mkDir(string $dirName)
    {
        if (!file_exists($dirName)) mkdir($dirName);
    }

    public static function save(string $file, $content = '')
    {
        if (!file_exists($file)) {
            $fp = fopen($file, "wb");
            fwrite($fp, $content);
            fclose($fp);
        }
    }

    public static function overwrite(string $file, $content = '')
    {
        $fp = fopen($file, "wb");
        fwrite($fp, $content);
        fclose($fp);
    }

    public static function compare(string $fn1, string $fn2): bool
    {
        define('READ_LEN', 4096);
        //   pass two file names
        //   returns TRUE if files are the same, FALSE otherwise

        if (filetype($fn1) !== filetype($fn2))
            return FALSE;

        if (filesize($fn1) !== filesize($fn2))
            return FALSE;

        if (!$fp1 = fopen($fn1, 'rb'))
            return FALSE;

        if (!$fp2 = fopen($fn2, 'rb')) {
            fclose($fp1);
            return FALSE;
        }

        $same = TRUE;
        while (!feof($fp1) and !feof($fp2))
            if (trim(fread($fp1, READ_LEN)) !== trim(fread($fp2, READ_LEN))) {
                $same = FALSE;
                break;
            }

        if (feof($fp1) !== feof($fp2))
            $same = FALSE;

        fclose($fp1);
        fclose($fp2);

        return $same;
    }
}
