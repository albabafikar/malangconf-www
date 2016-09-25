<?php
/**
 * Logger Collection Abstract
 *
 * @author  nawa <nawa@yahoo.com>
 * @version 1.0.0
 * @license MIT
 */
namespace MalangPhp\Site\Conf\Abstracts;

/**
 * Abstract Class LogMessageAbstract
 * @package MalangPhp\Site\Conf\Abstracts
 */
abstract class LogMessageAbstract
{
    /**
     * @const int error notice
     */
    const NOTICE   = E_NOTICE;

    /**
     * @const int error warning
     */
    const WARNING  = E_WARNING;

    /**
     * @const int error fatal
     */
    const ERROR = E_ERROR;

    /**
     * @const int alias of self::ERROR
     */
    const CRITICAL = E_ERROR;

    /**
     * @const int error deprecated
     */
    const DEPRECATED = E_DEPRECATED;

    /**
     * @const int unrecoverable error FAIL
     */
    const FAIL    = E_RECOVERABLE_ERROR;

    /**
     * @const int just save info as empty value selector
     */
    const INFO     = 0;

    /**
     * @const int ~self::INFO
     */
    const DEBUG    = -1;

    /**
     * @var array
     */
    protected $traced = [
        self::NOTICE  => [],
        self::WARNING => [],
        self::ERROR   => [],
        self::DEPRECATED => [],
        self::FAIL    => [],
        self::INFO    => [],
        self::DEBUG   => [],
    ];

    /**
     * LogAbstract constructor.
     *
     * @param string $message
     * @param int $type
     */
    public function __construct($message = null, $type = self::DEBUG)
    {
        $this->addInfo('Log initiated');
        if (!$message) {
            return;
        }
        $this->addTo($message, $type);
    }

    /**
     * Append Log
     *
     * @param string $message
     * @param int $type
     * @throws \InvalidArgumentException
     */
    protected function addTo($message, $type)
    {
        if (!is_string($message)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid log message type. Message must be as string %s given.',
                    gettype($message)
                ),
                E_USER_ERROR
            );
        }

        if (!is_numeric($type) || !is_int(abs($type)) || ! isset($this->traced[$type])) {
            throw new \InvalidArgumentException('Invalid log type.', E_USER_ERROR);
        }

        // add Key
        $time = microtime();
        $this->traced[$type][$time] = $message;
    }

    /**
     * @param string $message
     */
    public function addNotice($message)
    {
        $this->addTo($message, self::NOTICE);
    }

    /**
     * @return array
     */
    public function getNotice()
    {
        return $this->traced[self::NOTICE];
    }

    /**
     * Get Notice from key
     *
     * @param int $int
     * @return bool|mixed
     */
    public function getNoticeThatKey($int)
    {
        if (is_int($int)) {
            return isset($this->traced[self::NOTICE][$int]) ? $this->traced[self::NOTICE][$int] : false;
        }

        return false;
    }

    /**
     * @return void
     */
    public function clearNotice()
    {
        $this->traced[self::NOTICE] = [];
    }

    /**
     * @param int $key
     */
    public function removeNotice($key)
    {
        if (is_numeric($key) && isset($this->traced[self::NOTICE][$key])) {
            unset($this->traced[self::NOTICE][$key]);
        }
    }

    /**
     * @param string $message
     */
    public function addWarning($message)
    {
        $this->addTo($message, self::WARNING);
    }

    /**
     * @return array
     */
    public function getWarning()
    {
        return $this->traced[self::WARNING];
    }

    /**
     * Get Warning from key
     *
     * @param int $int
     * @return bool|mixed
     */
    public function getWarningThatKey($int)
    {
        if (is_int($int)) {
            return isset($this->traced[self::WARNING][$int]) ? $this->traced[self::WARNING][$int] : false;
        }

        return false;
    }

    /**
     * @return void
     */
    public function clearWarning()
    {
        $this->traced[self::WARNING] = [];
    }

    /**
     * @param int $key
     */
    public function removeWarning($key)
    {
        if (is_numeric($key) && isset($this->traced[self::WARNING][$key])) {
            unset($this->traced[self::WARNING][$key]);
        }
    }

    /**
     * @param string $message
     */
    public function addError($message)
    {
        $this->addTo($message, self::ERROR);
    }

    /**
     * @return array
     */
    public function getError()
    {
        return $this->traced[self::ERROR];
    }

    /**
     * Get Error from key
     *
     * @param int $int
     * @return bool|mixed
     */
    public function getErrorThatKey($int)
    {
        if (is_int($int)) {
            return isset($this->traced[self::ERROR][$int]) ? $this->traced[self::ERROR][$int] : false;
        }

        return false;
    }

    /**
     * @return void
     */
    public function clearError()
    {
        $this->traced[self::ERROR] = [];
    }

    /**
     * @param int $key
     */
    public function removeError($key)
    {
        if (is_numeric($key) && isset($this->traced[self::ERROR][$key])) {
            unset($this->traced[self::ERROR][$key]);
        }
    }

    /**
     * @param string $message
     */
    public function addDeprecated($message)
    {
        $this->addTo($message, self::DEPRECATED);
    }

    /**
     * @return array
     */
    public function getDeprecated()
    {
        return $this->traced[self::DEPRECATED];
    }

    /**
     * Get Deprecated from key
     *
     * @param int $int
     * @return bool|mixed
     */
    public function getDeprecatedThatKey($int)
    {
        if (is_int($int)) {
            return isset($this->traced[self::DEPRECATED][$int]) ? $this->traced[self::DEPRECATED][$int] : false;
        }

        return false;
    }

    /**
     * @return void
     */
    public function clearDeprecated()
    {
        $this->traced[self::DEPRECATED] = [];
    }

    /**
     * @param int $key
     */
    public function removeDeprecated($key)
    {
        if (is_numeric($key) && isset($this->traced[self::DEPRECATED][$key])) {
            unset($this->traced[self::DEPRECATED][$key]);
        }
    }

    /**
     * @param string $message
     */
    public function addFail($message)
    {
        $this->addTo($message, self::FAIL);
    }

    /**
     * @return array
     */
    public function getFail()
    {
        return $this->traced[self::FAIL];
    }

    /**
     * Get Fail from key
     *
     * @param int $int
     * @return bool|mixed
     */
    public function getFailThatKey($int)
    {
        if (is_int($int)) {
            return isset($this->traced[self::FAIL][$int]) ? $this->traced[self::FAIL][$int] : false;
        }

        return false;
    }

    /**
     * @return void
     */
    public function clearFail()
    {
        $this->traced[self::FAIL] = [];
    }

    /**
     * @param int $key
     */
    public function removeFail($key)
    {
        if (is_numeric($key) && isset($this->traced[self::FAIL][$key])) {
            unset($this->traced[self::FAIL][$key]);
        }
    }

    /**
     * @param string $message
     */
    public function addInfo($message)
    {
        $this->addTo($message, self::INFO);
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->traced[self::INFO];
    }

    /**
     * Get Fail from key
     *
     * @param int $int
     * @return bool|mixed
     */
    public function getInfoThatKey($int)
    {
        if (is_int($int)) {
            return isset($this->traced[self::INFO][$int]) ? $this->traced[self::INFO][$int] : false;
        }

        return false;
    }

    /**
     * @return void
     */
    public function clearInfo()
    {
        $this->traced[self::INFO] = [];
    }

    /**
     * @param int $key
     */
    public function removeInfo($key)
    {
        if (is_numeric($key) && isset($this->traced[self::INFO][$key])) {
            unset($this->traced[self::INFO][$key]);
        }
    }

    /**
     * @param $message
     */
    public function addDebug($message)
    {
        $this->addTo($message, self::DEBUG);
    }

    /**
     * @return array
     */
    public function getDebug()
    {
        return $this->traced[self::DEBUG];
    }

    /**
     * Get Debug from key
     *
     * @param int $int
     * @return bool|mixed
     */
    public function getDebugThatKey($int)
    {
        if (is_int($int)) {
            return isset($this->traced[self::DEBUG][$int]) ? $this->traced[self::DEBUG][$int] : false;
        }

        return false;
    }

    /**
     * @return array
     */
    public function clearDebug()
    {
        return $this->traced[self::DEBUG];
    }

    /**
     * @param int $key
     */
    public function removeDebug($key)
    {
        if (is_numeric($key) && isset($this->traced[self::DEBUG][$key])) {
            unset($this->traced[self::DEBUG][$key]);
        }
    }

    /**
     * Get micro time
     *
     * @param string $message
     * @param int    $errorType
     *
     * @return bool|int
     */
    public function getTimeFrom($message, $errorType)
    {
        if (!is_string($message) || ! is_int($errorType) || !isset($this->traced[$errorType])) {
            return false;
        }

        return array_search($message, $this->traced[$errorType], true);
    }

    /**
     * Search Message Contains
     *
     * @param string $message
     * @param int    $errorType
     * @return array|bool
     */
    public function searchForMessage($message, $errorType = null)
    {
        $err = [];
        if ($errorType === null) {
            foreach (array_keys($this->traced) as $v) {
                $err[$v] = $this->searchForMessage($message, $v);
            }
            return $err;
        }

        if (!is_string($message) || ! is_int($errorType) || !isset($this->traced[$errorType])) {
            return false;
        }

        foreach ($this->traced[$errorType] as $key => $value) {
            if (stripos($value, $message) !== false) {
                $err[$key] = $value;
            }
        }

        return $err;
    }

    /**
     * Getting All Data
     *
     * @return array
     */
    public function all()
    {
        return $this->traced;
    }

    /**
     * Alias Getting All Data
     *
     * @return array
     */
    public function getAll()
    {
        return $this->traced;
    }

    /**
     * @param string $message
     */
    public function debug($message)
    {
        $this->addDebug($message);
    }

    /**
     * @param string $message
     */
    public function deprecated($message)
    {
        $this->addDeprecated($message);
    }

    /**
     * @param string $message
     */
    public function error($message)
    {
        $this->addError($message);
    }

    /**
     * @param string $message
     */
    public function info($message)
    {
        $this->addInfo($message);
    }

    /**
     * @param string $message
     */
    public function fail($message)
    {
        $this->addFail($message);
    }

    /**
     * @param string $message
     */
    public function notice($message)
    {
        $this->addNotice($message);
    }

    /**
     * @param string $message
     */
    public function warning($message)
    {
        $this->addWarning($message);
    }
}
