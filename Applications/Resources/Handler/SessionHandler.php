<?php
/**
 * Session Handler
 *
 * @author  nawa <nawa@yahoo.com>
 * @version 1.0.0
 * @license MIT
 */
namespace MalangPhp\Site\Conf\Handler;

/**
 * Class SessionHandler
 * @package MalangPhp\Site\Conf\Handler
 */
class SessionHandler implements \SessionHandlerInterface
{
    /**
     * Constant Driver
     */
    const DRIVER_INTERNAL = 'Internal';
    const DRIVER_FILE = 'File';
    const DRIVER_DB   = 'Database';

    /**
     * @var \SessionHandlerInterface
     */
    protected $driver;

    /**
     * @var string
     */
    protected $selected_driver = self::DRIVER_INTERNAL;

    /**
     * SessionHandler constructor.
     * @param string $driver
     * @param string $cacheDir full path to save session
     */
    public function __construct($driver = self::DRIVER_INTERNAL, $cacheDir = null)
    {
        /**
         * Fallback Session Driver
         */
        if (! is_string($driver)
            || !in_array(ucwords($driver), [self::DRIVER_DB, self::DRIVER_FILE, self::DRIVER_INTERNAL])
        ) {
            $driver = $this->selected_driver;
        }

        $this->selected_driver = $driver;
        $driver_class = __NAMESPACE__ . "\\SessionDriver\\Session{$driver}";
        $this->driver = new $driver_class($cacheDir);
    }

    /**
     * Close the session
     * @link http://php.net/manual/en/sessionhandlerinterface.close.php
     *
     * @return bool
     */
    public function close()
    {
        return $this->driver->close();
    }


    /**
     * Destroy a session
     * @link http://php.net/manual/en/sessionhandlerinterface.destroy.php
     *
     * @param string $session_id The session ID being destroyed.
     * @return bool
     */
    public function destroy($session_id)
    {
        return $this->driver->destroy($session_id);
    }

    /**
     * Cleanup old sessions
     * @link http://php.net/manual/en/sessionhandlerinterface.gc.php
     *
     * @param int $maxlifetime
     * @return bool
     */
    public function gc($maxlifetime)
    {
        return $this->driver->gc($maxlifetime);
    }

    /**
     * Initialize session
     * @link http://php.net/manual/en/sessionhandlerinterface.open.php
     *
     * @param string $save_path The path where to store/retrieve the session.
     * @param string $name The session name.
     * @return bool
     */
    public function open($save_path, $name)
    {
        return $this->driver->open($save_path, $name);
    }

    /**
     * Read session data
     * @link http://php.net/manual/en/sessionhandlerinterface.read.php
     *
     * @param string $session_id The session id to read data for.
     * @return string
     */
    public function read($session_id)
    {
        return $this->driver->read($session_id);
    }

    /**
     * Write session data
     * @link http://php.net/manual/en/sessionhandlerinterface.write.php
     *
     * @param string $session_id The session id.
     * @param string $session_data
     * @return bool
     */
    public function write($session_id, $session_data)
    {
        return $this->driver->write($session_id, $session_data);
    }
}
