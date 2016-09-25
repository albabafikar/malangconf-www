<?php
/**
 * Shutdown Handler
 *      Handle file execution on the end of php worker
 *
 * @author  nawa <nawa@yahoo.com>
 * @version 1.0.0
 * @license MIT
 */
namespace MalangPhp\Site\Conf\Handler;

use Pentagonal\Hookable\Hookable;

/**
 * Class ShutDown
 * @package MalangPhp\Site\Conf\Handler
 */
final class ShutDown
{
    /**
     * @var Hookable
     */
    private static $hook;

    /**
     * @var ShutDown
     */
    private static $instance;

    /**
     * ShutDown constructor.
     */
    public function __construct()
    {
        if (!isset(self::$hook)) {
            self::$hook = new Hookable();
            $c =& $this;
            \register_shutdown_function(function () use ($c) {
                $c->doingShutDownCallback();
            });
        }
    }

    /**
     * @return ShutDown
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Add hook to shutdown
     *
     * @param callable $callable
     */
    public function add($callable)
    {
        if (is_callable($callable)) {
            self::$hook->add('shutdown', $callable, 10);
        }
    }

    /**
     * Remove Hooks
     *
     * @param callable $function_to_remove
     */
    public function remove($function_to_remove = null)
    {
        if ($function_to_remove && is_callable($function_to_remove)) {
            self::$hook->remove('shutdown', $function_to_remove);
        }
    }

    /**
     * ShutDown Callback
     */
    private function doingShutDownCallback()
    {
        self::$hook->call('shutdown');
    }
}
