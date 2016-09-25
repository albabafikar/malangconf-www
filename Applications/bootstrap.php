<?php
/**
 * PSR 0 Auto Loader
 */
return spl_autoload_register(function ($className) {
    $baseDir   = __DIR__ . '/Resources/';
    /**
     * @var string Name Space
     */
    $nameSpace = 'MalangPhp\\Site\\Conf\\';
    /**
     * Trimming Name Space
     */
    $className = ltrim($className, '\\');
    /**
     * next autoload that class is not apart of application name space
     */
    if (stripos($className, $nameSpace) !== 0) {
        return;
    }
    /**
     * Getting Class File Name
     */
    $className = substr(str_replace('\\', '/', $className), strlen($nameSpace));
    if (file_exists($baseDir . $className . '.php')) {
        /** @noinspection PhpIncludeInspection */
        require_once($baseDir . $className . '.php');
    }
});
