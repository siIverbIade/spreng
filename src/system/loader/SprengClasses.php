<?php

namespace Spreng\system\loader;

use Spreng\config\GlobalConfig;
use Spreng\system\utils\FileUtils;

/**
 * SprengClasses
 */
class SprengClasses
{
    private static function isInstanceOf(string $phpFile, string $sprengClass): bool
    {
        if (FileUtils::isPhpFile($phpFile)) {
            $tokens = token_get_all(file_get_contents($phpFile));
            $extends = 0;
            foreach ($tokens as $i => $v) {
                if ($v[0] !== T_WHITESPACE && $extends !== 0) {
                    $className = $v[1];
                    break;
                }
                if ($v[0] == T_EXTENDS) {
                    $extends = $i;
                }
            }
            if (!isset($className)) return false;
            $uses = '';
            foreach ($tokens as $i => $v) {
                if (isset($v[1])) $uses .= $v[1];
                if ($v[0] == T_CLASS) break;
            }

            if (strpos(trim($uses), "use $sprengClass") !== false && FileUtils::fileName($sprengClass) == $className) return true;
        }
        return false;
    }

    public static function scanFromSource(string $baseFolder, string $sprengClass): array
    {
        $initialDir = GlobalConfig::getSystemConfig()->getSourcePath() . "/$baseFolder";
        $filter = function ($path) use ($baseFolder, $sprengClass, $initialDir) {
            if (self::isInstanceOf($path, $sprengClass)) {
                $class = FileUtils::fileName($path);
                $currDir = FileUtils::fileDirName($path);
                $full = str_replace($initialDir, '', $currDir . "\\" . $class);
                $srcClass = GlobalConfig::getSystemConfig()->getSourceClass();
                $full = str_replace('\\\\', '\\', $srcClass . $baseFolder . $full);
                return [str_replace('\\\\', '\\', str_replace('/', '\\', $full)), $sprengClass];
            };
            return [null, null];
        };
        return FileUtils::dirToArray($initialDir, $filter);
    }
}
