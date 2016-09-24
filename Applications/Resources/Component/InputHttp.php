<?php
/**
 * Input HTTP Collection API
 *
 * @author  nawa <nawa@yahoo.com>
 * @version 1.0.0
 * @license MIT
 */
namespace MalangPhp\Site\Conf\Component;

use MalangPhp\Site\Conf\Interfaces\InputRequestFactoryInterface;
use Slim\Collection;
use Slim\Http\Environment;
use Slim\Http\Headers;

/**
 * Class InputHttp
 * @package MalangPhp\Site\Conf\Component
 */
class InputHttp implements InputRequestFactoryInterface
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var Headers
     */
    protected $header_environment;

    /**
     * @var string
     */
    protected static $phpInput;

    /**
     * InputComponent constructor.
     */
    final public function __construct()
    {
        /**
         * @var Headers
         */
        $this->header_environment = Headers::createFromEnvironment(Environment::mock($_SERVER));
        $headers = [];
        foreach ($this->header_environment as $item) {
            $headers[$item['originalKey']] = reset($item['value']);
        };

        $digest = $this->httpDigestParse(
            isset($_SERVER['PHP_AUTH_DIGEST']) ? $_SERVER['PHP_AUTH_DIGEST'] : false
        ) ?: [];

        /**
         * Parse Str
         * @var $array_input
         */
        parse_str($this->phpInput(), $array_input);
        $this->collection = new Collection(
            [
                'server'  => new ArrayStringParser($_SERVER),
                'post'    => new ArrayStringParser($_POST),
                'file'    => new ArrayStringParser($_FILES),
                'get'     => new ArrayStringParser($_GET),
                'cookie'  => new ArrayStringParser($_COOKIE),
                'request' => new ArrayStringParser($_REQUEST),
                'header'  => new ArrayStringParser($headers),
                'input'   => new ArrayStringParser($array_input),
                'digest'  => new ArrayStringParser($digest),
            ]
        );

        /**
         * Set method
         */
        $this->method = strtoupper($this->server('REQUEST_METHOD'));
    }

    /**
     * Is Auth Digest
     *
     * @return bool
     */
    public function isAuthDigest()
    {
        $auth = (string) $this->collection['header']->get('HTTP_AUTHORIZATION', '');
        return trim($auth) != '' && stripos(trim($auth), 'Digest ') === 0;
    }

    /**
     * Is Auth Basic
     *
     * @return bool
     */
    public function isAuthBasic()
    {
        $auth = (string) $this->collection['header']->get('HTTP_AUTHORIZATION', '');
        return trim($auth) != '' && stripos(trim($auth), 'Basic ') === 0;
    }

    /**
     * @param string $txt http digest $txt
     *
     * @return array|bool
     */
    protected function httpDigestParse($txt)
    {
        if (! is_string($txt)) {
            return false;
        }

        $txt = trim($txt);
        preg_match_all('/([a-z0-9]+)\=[\'\"]/i', $txt, $array_split);
        if (! empty($array_split[1])) {
            $data  = [];
            $match_split = preg_split(
                '/(?:,\s+)?(' . implode('|', $array_split[1]) . ')=["\']/',
                $txt
            );
            if (count($array_split[1]) > count($match_split)) {
                array_shift($match_split);
            }
            $match_split = array_values($match_split);
            foreach ($match_split as $key => $v) {
                if (!isset($array_split[1][$key])) {
                    continue;
                }
                $data[$array_split[1][$key]] = substr($v, 0, -1);
            }

            return $data;
        }

        return false;
    }

    /**
     * Get Php Input value
     *
     * @return string
     */
    public function phpInput()
    {
        if (!isset(self::$phpInput)) {
            self::$phpInput = file_get_contents("php://input");
        }

        return self::$phpInput;
    }

    /**
     * Get Method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    public function methodIs($name)
    {
        is_string($name) && trim(strtoupper($name));
        return $this->getMethod() == $name;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function input($keyName = null, $default = null)
    {
        return $this->collection->get('input')->fetch($keyName, $default);
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function get($keyName = null, $default = null)
    {
        return $this->collection->get('get')->fetch($keyName, $default);
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function post($keyName = null, $default = null)
    {
        return $this->collection->get('post')->fetch($keyName, $default);
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function put($keyName = null, $default = null)
    {
        return $this->getMethod() == 'PUT' ? $this->input($keyName, $default) : $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function patch($keyName = null, $default = null)
    {
        return $this->getMethod() == 'PATCH' ? $this->input($keyName, $default) : $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function delete($keyName = null, $default = null)
    {
        return $this->getMethod() == 'DELETE' ? $this->input($keyName, $default) : $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function copy($keyName = null, $default = null)
    {
        return $this->getMethod() == 'COPY' ? $this->input($keyName, $default) : $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function options($keyName = null, $default = null)
    {
        return $this->getMethod() == 'OPTIONS' ? $this->input($keyName, $default) : $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function link($keyName = null, $default = null)
    {
        return $this->getMethod() == 'LINK' ? $this->input($keyName, $default) : $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function unlink($keyName = null, $default = null)
    {
        return $this->getMethod() == 'UNLINK' ? $this->input($keyName, $default) : $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function purge($keyName = null, $default = null)
    {
        return $this->getMethod() == 'PURGE' ? $this->input($keyName, $default) : $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function lock($keyName = null, $default = null)
    {
        return $this->getMethod() == 'LOCK' ? $this->input($keyName, $default) : $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function unlock($keyName = null, $default = null)
    {
        return $this->getMethod() == 'UNLOCK' ? $this->input($keyName, $default) : $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function propFind($keyName = null, $default = null)
    {
        return $this->getMethod() == 'PROPFIND' ? $this->input($keyName, $default) : $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function view($keyName = null, $default = null)
    {
        return $this->getMethod() == 'VIEW' ? $this->input($keyName, $default) : $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function connect($keyName = null, $default = null)
    {
        return $this->getMethod() == 'CONNECT' ? $this->input($keyName, $default) : $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function head($keyName = null, $default = null)
    {
        return $this->getMethod() == 'HEAD' ? $this->input($keyName, $default) : $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function trace($keyName = null, $default = null)
    {
        return $this->getMethod() == 'TRACE' ? $this->input($keyName, $default) : $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function file($keyName = null, $default = null)
    {
        return $this->collection->get('files')->fetch($keyName, $default);
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function files($keyName = null, $default = null)
    {
        return $this->file($keyName, $default);
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function header($keyName = null, $default = null)
    {
        return $this->collection->get('header')->fetch($keyName, $default);
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function headers($keyName = null, $default = null)
    {
        return $this->header($keyName, $default);
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function server($keyName = null, $default = null)
    {
        return $this->collection->get('server')->fetch($keyName, $default);
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function request($keyName = null, $default = null)
    {
        return $this->collection->get('request')->fetch($keyName, $default);
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function auth($keyName = null, $default = null)
    {
        if ($this->isAuthDigest()) {
            return $this->digest($keyName, $default);
        }

        if (is_string($keyName) && strtolower($keyName) == 'digest') {
            return $this->digest();
        }

        if (!isset($keyName) && $this->isAuthBasic()) {
            return [
                'PHP_AUTH_USER' => $this->server('PHP_AUTH_USER'),
                'PHP_AUTH_PW' => $this->server('PHP_AUTH_PW'),
            ];
        }

        if ($keyName == 'PHP_AUTH_PW' && $this->server('PHP_AUTH_PW') !== null
            || $keyName == 'PHP_AUTH_USER' && $this->server('PHP_AUTH_USER') !== null
        ) {
            return $this->server($keyName);
        }

        return $default;
    }

    /**
     * @param null|string   $keyName
     * @param mixed         $default
     *
     * @return mixed
     */
    public function digest($keyName = null, $default = null)
    {
        return $this->collection->get('digest')->fetch($keyName, $default);
    }
}
