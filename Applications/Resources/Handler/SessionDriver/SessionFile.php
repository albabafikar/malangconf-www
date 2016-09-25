<?php
/**
 * File Session Driver
 *
 * @author  nawa <nawa@yahoo.com>
 * @version 1.0.0
 * @license MIT
 */
namespace MalangPhp\Site\Conf\Handler\SessionDriver;

use MalangPhp\Site\Conf\Helper\FileDirect;
use MalangPhp\Site\Conf\Helper\Path;

/**
 * Class SessionFile
 * @package Pentagonal\Project\Haerde\Handler\SessionDriver
 */
class SessionFile extends \SessionHandler
{
    /**
     * @var string
     */
    protected $session_id;

    /**
     * @var string
     */
    protected $cache_dir;

    /**
     * @var string
     */
    protected $save_path;

    /**
     * @var FileDirect
     */
    protected $file;

    /**
     * @var string
     */
    protected $fingerPrint;

    /**
     * @var bool
     */
    protected $file_exists;

    /**
     * @var bool
     */
    protected $closed;

    /**
     * SessionFile constructor.
     *
     * @param string $cacheDir
     * @throws \ErrorException
     */
    public function __construct($cacheDir = null)
    {
        $this->file = new FileDirect();
        if (! $cacheDir) {
            $cacheDir = dirname(dirname(__DIR__)) .'/Cache';
        }

        $path = $cacheDir;
        $path = Path::trailSlashAsUnix($path);
        if (!$path || ! $this->file->isWritable($path)) {
            throw new \ErrorException('Session directory not write able for session file. Please fix ASAP');
        }
        $this->cache_dir = Path::trailSlashAsUnix($path) . 'Session/';
        if (!$this->file->exists($this->cache_dir)) {
            if (!$this->file->mkDir($this->cache_dir)) {
                throw new \ErrorException('Could not create session directory. Please fix ASAP');
            }
        } elseif ($this->file->exists($this->cache_dir)) {
            if (!$this->file->isDir($this->cache_dir)) {
                throw new \ErrorException('Directory Session not valid. Please fix ASAP');
            }
        }

        if (!$this->file->exists($this->cache_dir . 'index.html')) {
            $this->file->touch($this->cache_dir . 'index.html');
        }
        if (!$this->file->exists($this->cache_dir . '.htaccess')) {
            $this->file->writeContent($this->cache_dir . '.htaccess', "Deny From All");
        }
        $this->save_path = $this->cache_dir;
    }

    /**
     * Generate Session For File Name
     *
     * @access protected
     *
     * @param string $sessionId
     * @return string
     */
    protected function generateSessionFiloForName($sessionId)
    {
        return 'sess_' . sha1($sessionId);
    }

    /**
     * Get File Session Full Path
     *
     * @return string
     */
    protected function getFileSession()
    {
        return $this->save_path . $this->generateSessionFiloForName($this->session_id);
    }

    /**
     * Close the session
     * @link http://php.net/manual/en/sessionhandlerinterface.close.php
     *
     * @return bool
     */
    public function close()
    {
        if (! $this->closed) {
            $this->session_id = null;
            $this->file_exists = null;
        }
        $this->closed = true;
        return true;
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
        if ($this->close() === true) {
            if ($this->file->exists($this->save_path.$this->generateSessionFiloForName($session_id))) {
                $this->file->delete($this->save_path.$this->generateSessionFiloForName($session_id));
            }
            return true;
        } else {
            \clearstatcache();
            if ($this->file->exists($this->save_path.$this->generateSessionFiloForName($session_id))) {
                return $this->file->delete($this->save_path.$this->generateSessionFiloForName($session_id));
            }
            return true;
        }
    }

    /**
     * Cleanup old sessions
     * @link http://php.net/manual/en/sessionhandlerinterface.gc.php
     *
     * @param int $maxLifeTime
     * @return bool
     */
    public function gc($maxLifeTime)
    {
        $directory = Path::trailSlashAsUnix(dirname($this->getFileSession()));
        if (strpos($directory, $this->save_path)
            || !$this->file->isDir($directory) || ! $this->file->isWritable($directory)
        ) {
            # debug :
            #    "Session: Garbage collector couldn't list files under directory '" . $this->save_path

            return false;
        }
        $ts = time() - $maxLifeTime;
        foreach ((array) $this->file->directoryList($directory, false) as $key => $file) {
            if (strlen($file) === 45 && $this->file->isFile($directory.$file)
                && $this->file->mTime($directory.$file) > $ts
            ) {
                $this->file->delete($directory.$file);
            }
        }

        return true;
    }

    /**
     * Initialize session
     * @link http://php.net/manual/en/sessionhandlerinterface.open.php
     * @param string $save_path The path where to store/retrieve the session.
     * @param string $name The session name.
     * @return bool
     * @throws \ErrorException
     */
    public function open($save_path, $name)
    {
        $this->closed = false;
        $this->save_path = Path::trailSlashAsUnix($this->cache_dir . $save_path);
        if ($this->file->exists($this->save_path)) {
            if ($this->file->isDir($this->save_path)) {
                return $this->file->isWritable($this->save_path);
            }
            throw new \ErrorException('Directory Session not valid. Please fix ASAP');
        } else {
            if (!$this->file->mkDir($this->save_path)) {
                throw new \ErrorException('Could not create session directory. Please fix ASAP');
            }
        }

        return true;
    }

    /**
     * Read session data
     * @link http://php.net/manual/en/sessionhandlerinterface.read.php
     * @param string $session_id The session id to read data for.
     * @return string
     */
    public function read($session_id)
    {
        $this->session_id = $session_id;
        $session_data = '';

        $this->file_exists = $this->file->exists($this->getFileSession());
        if ($this->file_exists) {
            $session_data = $this->file->getContents($this->getFileSession());
            $this->fingerPrint = md5($session_data);
        } else {
            $this->file->touch($this->getFileSession());
            $this->fingerPrint = md5('');
        }

        return $session_data;
    }

    /**
     * Write session data
     * @link http://php.net/manual/en/sessionhandlerinterface.write.php
     * @param string $session_id The session id.
     * @param string $session_data
     * @return bool
     */
    public function write($session_id, $session_data)
    {
        // If the two IDs don't match, we have a session_regenerate_id() call
        // and we need to close the old handle and open a new one
        if ($session_id !== $this->session_id
            && ($this->close() === false || $this->read($session_id) === false)
        ) {
            return false;
        }

        $this->closed = false;
        if ($this->fingerPrint === md5($session_data)) {
            return ! ($this->file_exists && ! $this->file->touch($this->getFileSession()));
        }

        if (! $this->file_exists) {
            if (!$this->file->writeContent($this->getFileSession(), $session_data)) {
                # Fail :
                # 'Unable to write session data for ' . $session_id;
            }
        }

        $this->fingerPrint = md5($session_data);
        return true;
    }
}
