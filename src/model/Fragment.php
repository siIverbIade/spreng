<?php

declare(strict_types=1);

namespace Spreng\model;

use Twig\Lexer;
use Twig\Environment;
use Spreng\model\Template;
use Spreng\config\GlobalConfig;
use Spreng\system\loader\iNject;
use Twig\Loader\FilesystemLoader;
use Spreng\system\utils\FileUtils;

/**
 * Fragment
 */
abstract class Fragment  extends iNject implements Template
{
    private $rootPath;
    private $folder;
    private $template;
    private $links = [];
    private $scripts = [];
    private $assetsPath = '';

    public function __construct(string $template = '', string $folder = '', string $rootPath = '')
    {
        $this->rootPath = $rootPath;
        $this->folder = $folder;
        $this->template = $template;
        $this->assetsPath =  '/' . FileUtils::fileName(GlobalConfig::getSystemConfig()->getSourcePath()) . "/" . GlobalConfig::getModelConfig()->getAssetsFolder();
    }

    private function set(array $obj): string
    {
        $template = $this->template;
        $modelConfig = GlobalConfig::getModelConfig();

        if ($this->rootPath == '') $rootPath = GlobalConfig::getSystemConfig()->getSourcePath() . '/' . $modelConfig->getTemplateRoot();
        else $rootPath = $this->rootPath;

        $env = new Environment(new FilesystemLoader("$rootPath/$this->folder"), [
            'auto_reload' => $modelConfig->isAutoReloadEnabled(),
            'cache' => "$rootPath/$this->folder/compilation_cache",
        ]);

        $env->setLexer(new Lexer($env, [
            'tag_block' => ['@@', '@@'],
            'tag_variable' => ['$', '$'],
        ]));
        return $env->render($template . '.html', $obj);
    }

    public function setReferences(string $assetsPath)
    {
        $this->assetsPath = $assetsPath;
    }

    public function addScript(string $type, string $src)
    {
        $this->scripts[] = ['type' => $type, 'src' => "$this->assetsPath/$src"];
    }

    public function addLink(string $rel, string $href)
    {
        $this->links[] = ['rel' => $rel, 'href' => "$this->assetsPath/$href"];
    }

    public function addScripts(array $scripts)
    {
        array_walk_recursive($scripts, function (&$k, $i) {
            if ($i == 'src') {
                $k = $this->assetsPath . '/' . $k;
            }
        });
        $this->scripts = array_merge($this->scripts, $scripts);
    }

    public function addLinks(array $links)
    {
        array_walk_recursive($links, function (&$k, $i) {
            if ($i == 'href') {
                $k = $this->assetsPath . '/' . $k;
            }
        });
        $this->links = array_merge($this->links, $links);
    }

    public function scripts()
    {
        return $this->scripts;
    }

    public function links()
    {
        return $this->links;
    }

    public function show()
    {
        session_reset();
        return $this->set(get_object_vars($this));
    }

    public function build()
    {
    }
}
