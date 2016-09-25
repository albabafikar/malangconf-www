<?php
/**
 * Application Class
 *
 * @license MIT
 */
namespace MalangPhp\Site\Conf;

use FastRoute\Dispatcher;
use MalangPhp\Site\Conf\Component\BenchMark;
use MalangPhp\Site\Conf\Component\ConfigCollection;
use MalangPhp\Site\Conf\Component\InputHttp;
use MalangPhp\Site\Conf\Helper\LogMessage;
use MalangPhp\Site\Conf\Helper\Path;
use Slim\App as Slim;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Views\PhpRenderer;

/**
 * Class App
 * @package MalangPhp\Site\Conf
 */
class App implements \ArrayAccess
{
    /**
     * @var Slim
     */
    protected $slimApp;

    /**
     * @var string
     */
    protected $selectedEnvironment;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var boolean
     */
    protected $hasInit;

    /**
     * protected Applications
     *
     * @var array
     */
    protected $protectedApplication = [
        'slim',
        'app',
        'log',
        'benchmark',
        'config_factory',
        'config',
        'settings',
        'input',
        'view'
    ];

    /**
     * Config Applications order
     *
     * @var array
     */
    protected $appLoads = [
        'Container'  => 'Containers.php',
        'Middleware' => 'Middleware.php',
        'Routes'     => 'Routes.php',
    ];

    /**
     * @var App
     */
    protected static $instance;

    /**
     * App constructor.
     * @param array|null $configs
     * @param null $selectedEnvironment
     */
    public function __construct(array $configs = null, $selectedEnvironment = null)
    {
        if (is_null($configs)) {
            // include Default Config File
            $configs = include dirname(__DIR__) .'/Configs/Config.php';
        }
        $this->config = $configs;
        $this->selectedEnvironment = $selectedEnvironment;
        static::$instance = $this;
    }

    /**
     * Initial Process
     */
    protected function initProcess()
    {
        $this->hasInit = $this->slimApp instanceof Slim;
        if ($this->hasInit) {
            return;
        }

        $selectedEnvironment = $this->selectedEnvironment;
        $configFactory = $this->config;

        /**
         * Init
         */
        $log       = new LogMessage();
        $benchMark = new BenchMark();
        $log->info('Application initialized.');
        $benchMark->start('app');
        $configFactory = new ConfigCollection($configFactory);

        /**
         * Check Config
         */
        if (count($configFactory) === 0) {
            throw new \InvalidArgumentException(
                "Configuration empty",
                E_USER_ERROR
            );
        }

        /**
         * Check Selected Environment
         */
        if ($selectedEnvironment && ! is_string($selectedEnvironment)) {
            throw new \InvalidArgumentException(
                "Selected Environment Must be as a string.",
                E_USER_ERROR
            );
        }

        if (! $selectedEnvironment || ! $configFactory->has($selectedEnvironment)) {
            $configs = $configFactory->all();
            $selectedEnvironment = key($configs);
        }

        $config = $configFactory->get($selectedEnvironment);
        $displayErrorDetails = !empty($config->get('debug'));

        // app Directory
        $appDir        = Path::cleanAsUnix(dirname(__DIR__)) . '/';
        $settings = [
            'deploymentEnvironment' => $selectedEnvironment,
            'determineRouteBeforeAppMiddleware' => true,
            'displayErrorDetails' => $displayErrorDetails,
            'resourceDir'   => Path::cleanAsUnix(__DIR__) . '/',
            'applicationDir'=> $appDir,
            'dataDir'       => $appDir .'Data/',
            'controllerDir' => $appDir .'Controller/',
            'configDir'     => $appDir .'Configs/',
            'cacheDir'      => $appDir .'Cache/',
            'viewDir'       => $appDir .'View/',
        ];
        $this->slimApp = new Slim([
            /**
             * FallBack ApplicationInitiator
             *
             * @return App
             */
            'app'           => $this,
            'benchmark'     => $benchMark,
            'config'        => $config,
            'config_factory'=> $configFactory,
            'environment'   => Environment::mock($this->portServerManipulation()),
            'input'         => function () {
                return new InputHttp();
            },
            'view'          => function ($c) {
                $setting = $c['settings'];
                return new PhpRenderer($setting['viewDir']);
            },
            'log'           => $log,
            'settings'      => $settings
        ]);

        /**
         * Set Application
         */
        $container = $this->slimApp->getContainer();
        $container['slim'] = function () {
            return $this->slimApp;
        };
        /**
         * Fix Rewrite
         */
        $env = clone $container->get('environment');
        $env['SCRIPT_NAME'] = dirname($env['SCRIPT_NAME']);
        $request = clone $container->get('request');
        unset($container['request']);
        /** @noinspection PhpUndefinedMethodInspection */
        $container['request'] = $request->withUri(Uri::createFromEnvironment($env));

        /**
         * Load configs
         */
        foreach ($this->appLoads as $key => $value) {
            $this->appLoads[$key] = "{$settings['configDir']}";
            if ($selectedEnvironment) {
                $this->appLoads[$key] .= "{$selectedEnvironment}/{$value}";
            } else {
                $this->appLoads[$key] .= "{$value}";
            }

            if (file_exists($this->appLoads[$key])) {
                /** @noinspection PhpIncludeInspection */
                require_once $this->appLoads[$key];
                continue;
            }
            throw new \ErrorException(
                sprintf(
                    "Config key for %s does not exists",
                    $key
                ),
                E_USER_ERROR
            );
        }
    }

    /**
     * Detecting & Fix Environment on some cases
     *     Default Environment uses $_SERVER to attach
     *     just to fix https
     *
     * @return array
     */
    protected function portServerManipulation()
    {
        static $server;
        if (isset($server)) {
            return $server;
        }

        $server = $_SERVER;
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off'
            // hide behind proxy / maybe cloud flare cdn
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
            || !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off'
        ) {
            // detect if non standard protocol
            if ($_SERVER['SERVER_PORT'] == 80
                && (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
                    || isset($_SERVER['HTTP_FRONT_END_HTTPS'])
                )
            ) {
                $_SERVER['SERVER_PORT_MANIPULATED'] = 443;
                $server['SERVER_PORT'] = 443;
                $server['SERVER_PORT_MANIPULATED'] = 80;
            }
            // fixing HTTPS Environment
            $_SERVER['HTTPS_MANIPULATED'] = 'on';
            $server['HTTPS'] = 'on';
            $server['HTTPS_MANIPULATED'] = 'on';
        }

        return $server;
    }

    /**
     * Get Registered Config
     *
     * @return array
     */
    public function getArrayLoadedConfig()
    {
        return $this->appLoads;
    }

    /**
     * Getting Slim Container
     *      Instantiate @uses App::run()
     * @return \Interop\Container\ContainerInterface
     */
    public function getContainer()
    {
        return $this->slimApp->getContainer();
    }

    /**
     * Get Slim Instance
     *
     * @return Slim
     */
    public function slim()
    {
        return $this->slimApp;
    }

    /**
     * Get Current Matched Route
     *
     * @return array
     */
    public function getCurrentRouteInfo()
    {
        return $this->get('router')->dispatch($this->get('request'));
    }

    /**
     * Check if Route Found
     *
     * @return bool
     */
    public function isCurrentRouteFound()
    {
        $dispatchedRoute = $this->getCurrentRouteInfo();
        return $dispatchedRoute[0] == Dispatcher::FOUND;
    }

    /**
     * Check Container
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->getContainer()->has($name);
    }

    /**
     * Get Container value
     *
     * @param string $name
     * @param string $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if ($this->has($name)) {
            return $this->getContainer()->get($name);
        }

        return $default;
    }

    /**
     * Set Container
     *
     * @param string $name
     * @param string $value
     */
    public function set($name, $value)
    {
        $this->remove($name);
        $container = $this->getContainer();
        $container[$name] = $value;
    }

    /**
     * Unset Container
     *
     * @param string $name
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            if (in_array($name, $this->protectedApplication)) {
                throw new \InvalidArgumentException(
                    sprintf('Can not remove protected application %s', $name),
                    E_USER_WARNING
                );
            }
            $container = $this->getContainer();
            unset($container[$name]);
        }
    }

    /**
     * @return App
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set Config
     *      No affected if Application has run
     *
     * @param array $config
     * @return App
     */
    public function setConfig(array $config)
    {
        if ($this->hasInit) {
            $this->get('log')->debug('Try to set environment, that with existing application has been run.');
            return $this;
        }
        $this->config = $config;
        return $this;
    }

    /**
     * Set Environment
     *      No affected if Application has run
     *
     * @param string $environment
     * @return App
     */
    public function setEnvironment($environment)
    {
        if ($this->hasInit) {
            $this->get('log')->debug('Try to set environment, that with existing application has been run.');
            return $this;
        }

        $this->selectedEnvironment = $environment;
        return $this;
    }

    /**
     * Getting Environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->selectedEnvironment;
    }

    /**
     * Running Application
     *
     * @param bool $silent
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function run($silent = false)
    {
        $instance = self::getInstance();
        //prevent multiple App running
        if ($instance->hasInit) {
            return $instance->get('response');
        }
        $instance->initProcess();
        return $instance->slimApp->run($silent);
    }

    /**
     * Magic Method Get
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Magic Method Set Container
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Magic Method Isset
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset An offset to check for.
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset The offset to retrieve.
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset The offset to unset.
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}
