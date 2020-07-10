<?php

namespace Spreng\system\boot;

use Exception;
use Dotenv\Dotenv;
use Spreng\system\Server;
use Spreng\system\log\Logger;
use Spreng\config\GlobalConfig;
use Spreng\system\utils\FileUtils;
use Spreng\system\collections\InitializerList;

/**
 * InitializeHandler
 */
class InitializeHandler
{
    private $initializers;
    private $classes;

    public function __construct(bool $prodEnv = false)
    {
        $documentRoot = Server::getDocumentRoot();
        $sc = GlobalConfig::getSystemConfig();

        if ($prodEnv) FileUtils::overwrite($_SERVER['DOCUMENT_ROOT'] . '/.env', GlobalConfig::global(true)->getAsEnv());

        $firstRun = $sc->getFirstRun();
        if ($firstRun) {

            if (!file_exists($documentRoot . '/composer.json')) {
                echo "This is an composer based application but file composer.json is missing in your document root folder '$documentRoot'.";
                exit;
            }

            $htaccess = dirname(__DIR__, 2) . '/config/resources/.htaccess';

            if (!file_exists($documentRoot . '/.htaccess')) {
                try {
                    copy($htaccess, $documentRoot . '/.htaccess');
                } catch (Exception $e) {
                    echo "Spreng could not copy file '$htaccess' to the root folder '$documentRoot'. Please, do it manually.";
                    exit;
                }
            } else {
                if (!FileUtils::compare($htaccess, $documentRoot . '/.htaccess')) {
                    echo "Apache's .htaccess found in rootDir '$documentRoot' is not suited for this application. Please delete it and let spreng create a new one for you";
                    exit;
                }
            }

            $composerConfig = GlobalConfig::getComposerConfig();
            if (!$composerConfig->isAutoloadSet()) {
                echo "Please ensure your composer.json has autoload/psr-4 property set.";
                exit;
            }

            $sc->setSourceClass($composerConfig->getPsr4Name());
            $sourcePath = $documentRoot . '/' . $composerConfig->getPsr4Source();
            $firstIntro = $sc->getIntro();

            if ($firstIntro) {
                try {
                    FileUtils::mkDir($sourcePath);
                    FileUtils::save($sourcePath . '/MyFirstController.php', self::MyFirstController(str_replace('\\', '', $composerConfig->getPsr4Name())));
                    try {
                        echo exec('composer dump-autoload -o') . '...</br>';
                    } catch (Exception $e) {
                        echo "Autoload dump couldn't be done now, please run 'composer dump-autoload -o' in order to update your classMap, then refresh this screen.";
                    }
                } catch (Exception $e) {
                    echo "Source folder '" . $sourcePath . "' could not be created, please do it manually.";
                }
                echo "Setup First Boot Intro is done. Click Refresh to start...";
                $sc->setIntro(false);
            }
            $sc->setSourcePath($sourcePath);
            $sc->setFirstRun(false);
            GlobalConfig::setSystemConfig($sc);

            if ($firstIntro) exit;

            $this->initializers = new InitializerList();
            $this->classes = GlobalConfig::getAllImplementationsOf(GlobalConfig::getSystemConfig()->getServicePath(), Initializer::class);
            $this->registerProcesses();
        }
        self::startEnvironment($documentRoot);
        if (!file_exists($sc->getSourcePath())) {
            echo "Read configuration failed in setup.json: 'source_path' was not found.";
            exit;
        }
    }

    private static function startEnvironment(string $configFile)
    {
        $dotenv = Dotenv::createMutable($configFile);
        $dotenv->load();
        //Logger::debug($_ENV['APPLICATION'], true);
    }

    private static function MyFirstController(string $namespace): string
    {
        return "<?php\n
namespace $namespace;\n
use Spreng\http\Controller;\nuse Spreng\http\HttpResponse;\n
class MyFirstController extends Controller\n{\n
public static function home(): HttpResponse\n{\nreturn new HttpResponse(function () {\nreturn '<h2>Hello World!</h2> <p>Congratulations, your application seems to be working :)</p>';\n}, '/');\n}\n}?>\n";
    }

    private function registerProcesses()
    {
        foreach ($this->classes as $class => $parentClass) {
            $this->initializers->add(new $class);
        }
    }

    public function run()
    {
        if (isset($this->initializers)) {
            foreach ($this->initializers->getAll() as $prc) {
                $prcShifted = $prc->getFn();
                foreach ($prcShifted as $name) {
                    $prc->{$name}();
                }
            }
        }
    }
}
