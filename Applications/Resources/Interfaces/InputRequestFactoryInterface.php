<?php
/**
 * Input HTTP Collection API Interfaces
 *
 * purpose to @uses \MalangPhp\Site\Conf\Component\InputHttp
 *
 * @author  nawa <nawa@yahoo.com>
 * @version 1.0.0
 * @license MIT
 */
namespace MalangPhp\Site\Conf\Interfaces;

/**
 * Interface InputRequestFactoryInterface
 * @package MalangPhp\Site\Conf\Interfaces
 */
interface InputRequestFactoryInterface
{
    /* ---------------------
        REQUEST METHOD
    --------------------- */

    public function input($keyName = null, $default = null);
    public function get($keyName = null, $default = null);
    public function post($keyName = null, $default = null);
    public function put($keyName = null, $default = null);
    public function patch($keyName = null, $default = null);
    public function delete($keyName = null, $default = null);
    public function copy($keyName = null, $default = null);
    public function head($keyName = null, $default = null);
    public function options($keyName = null, $default = null);
    public function link($keyName = null, $default = null);
    public function unlink($keyName = null, $default = null);
    public function purge($keyName = null, $default = null);
    public function lock($keyName = null, $default = null);
    public function unlock($keyName = null, $default = null);
    public function propFind($keyName = null, $default = null);
    public function view($keyName = null, $default = null);
    // custom
    public function connect($keyName = null, $default = null);
    public function trace($keyName = null, $default = null);

    /* ---------------------
        SERVER METHOD
    --------------------- */
    public function file($keyName = null, $default = null);
    public function header($keyName = null, $default = null);
    public function server($keyName = null, $default = null);
    public function request($keyName = null, $default = null);

    /* ---------------------
        SERVER AUTH
    --------------------- */
    public function auth($keyName = null, $default = null);
    public function digest($keyName = null, $default = null);
}
