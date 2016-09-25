<?php
namespace MalangPhp\Site\Conf\Helper;

use MalangPhp\Site\Conf\Abstracts\FileAbstract;

/**
 * Class FileDirect
 * @package MalangPhp\Site\Conf\Helper
 */
class FileDirect extends FileAbstract
{
    /**
     * @var string
     */
    protected $mode = 'direct';

    /**
     * Check if file / directory Exists
     *
     * @param string $path
     *
     * @return bool
     */
    public function exists($path)
    {
        return ! is_string($path) ?: file_exists($path);
    }

    /**
     * Check if file Exists and it is File
     *
     * @param string $path
     *
     * @return bool
     */
    public function isFile($path)
    {
        return ! is_string($path) ?: @is_file($path);
    }

    /**
     * Check if Directory exists and it is file
     *
     * @param string $dir
     *
     * @return bool
     */
    public function isDir($dir)
    {
        return ! is_string($dir) ?: is_dir($dir);
    }

    /**
     * @param string $path
     *
     * @return bool null if invalid
     */
    public function isReadable($path)
    {
        return ! is_string($path) ?: @is_readable($path);
    }

    /**
     * Check if Writable
     *
     * @param string $path
     *
     * @return bool
     */
    public function isWritable($path)
    {
        if ($this->exists($path)) {
            return is_writable($path);
        }

        return false;
    }

    /**
     * Get Content
     *
     * @param string       $path
     * @param null|integer $length null if get All
     *
     * @return bool|string
     */
    public function getContents($path, $length = null)
    {
        if ($this->isFile($path) && $this->isReadable($path)) {
            if ($length === null || is_numeric($length) && $length > 0) {
                return @file_get_contents($path, null, null, null, $length);
            }
        }

        return false;
    }

    /**
     * Get Content as Array
     *
     * @param string $path
     *
     * @return array|bool
     */
    public function getContentsAsArray($path)
    {
        if ($this->isFile($path) && $this->isReadable($path)) {
            return @file($path);
        }
        return false;
    }

    /**
     * Write content into file
     *
     * @param string    $path
     * @param string    $data
     * @param int|bool  $mode Optional. The file permissions as octal number, usually 0644.
     *
     * @return bool
     */
    public function writeContent($path, $data, $mode = false)
    {
        if (!is_string($path) || $this->isDir($path)) {
            return false;
        }

        $fp = @fopen($path, 'wb');
        if (! $fp) {
            return false;
        }

        //safe write contents
        if (!is_string($data)) {
            $data = print_r($data, true);
        }

        // set safe encoding
        $this->mbStringBinarySafeEncoding();

        $data_length = strlen($data);

        // handle separate of 4096 bytes
        // elseof the others could to handle big file data
        if ($data_length <= 4096) {
            $bytes_written = fwrite($fp, $data);
        } else {
            $bytes_written = 0;
            while ($bytes_written < $data_length) {
                $write = fwrite($fp, substr($data, $bytes_written), 4096);
                $bytes_written += $write;
                if (! $write) {
                    break;
                }
            }
        }

        // reset safe encoding
        $this->mbStringBinarySafeEncoding(true);
        fclose($fp);

        if ($data_length !== $bytes_written) {
            return false;
        }

        $this->chMod($path, $mode);
        return true;
    }

    /**
     * Set the mbstring internal encoding to a binary safe encoding when func_overload
     * is enabled.
     *
     * When mbstring.func_overload is in use for multi-byte encodings, the results from
     * strlen() and similar functions respect the utf8 characters, causing binary data
     * to return incorrect lengths.
     *
     * This function overrides the mbstring encoding to a binary-safe encoding, and
     * resets it to the users expected encoding afterwards through the
     * `reset_mbstring_encoding` function.
     *
     * It is safe to recursively call this function, however each
     *
     * @param bool $reset Optional. Whether to reset the encoding back to a previously-set encoding.
     *                    Default false.
     */
    private function mbStringBinarySafeEncoding($reset = false)
    {
        static $encodings = [];
        static $overloaded = null;

        if (is_null($overloaded)) {
            $overloaded = function_exists('mb_internal_encoding') && (ini_get('mbstring.func_overload') & 2);
        }

        if (false === $overloaded) {
            return;
        }

        if (! $reset) {
            $encoding = mb_internal_encoding();
            array_push($encodings, $encoding);
            mb_internal_encoding('ISO-8859-1');
        }

        if ($reset && $encodings) {
            $encoding = array_pop($encodings);
            mb_internal_encoding($encoding);
        }
    }

    /**
     * Gets the current working directory
     *
     * @return string|bool the current working directory on success, or false on failure.
     */
    public function cwd()
    {
        return @getcwd();
    }

    /**
     * Change directory
     *
     * @param string $dir The new current directory.
     *
     * @return bool Returns true on success or false on failure.
     */
    public function chdir($dir)
    {
        if ($this->isDir($dir)) {
            return @chdir($dir);
        }

        return false;
    }

    /**
     * Changes file group
     *
     * @param string $path      Path to the file.
     * @param mixed  $group     A group name or number.
     * @param bool   $recursive Optional. If set True changes file group recursively. Default false.
     *
     * @return bool Returns true on success or false on failure.
     */
    public function chGrp($path, $group, $recursive = false)
    {
        if (! $this->exists($path)) {
            return false;
        }
        if (! $recursive) {
            return @chgrp($path, $group);
        }
        if (! $this->isDir($path)) {
            return @chgrp($path, $group);
        }
        // Is a directory, and we want recursive
        $path      = Path::trailSlashAsUnix($path);
        $file_list = $this->directoryList($path);
        foreach ($file_list as $filename) {
            $this->chGrp($path . $filename, $group, $recursive);
        }

        return true;
    }

    /**
     * Changes filesystem permissions
     *
     * @param string    $file      Path to the file.
     * @param int|bool  $mode      Optional. The permissions as octal number, usually 0644 for files,
     *                          0755 for dirs. Default false.
     * @param bool      $recursive Optional. If set True changes file group recursively. Default false.
     *
     * @return bool Returns true on success or false on failure.
     */
    public function chMod($file, $mode = false, $recursive = false)
    {
        if (! $mode) {
            if ($this->isFile($file)) {
                $mode = self::CHMOD_FILE;
            } elseif ($this->isDir($file)) {
                $mode =  self::CHMOD_DIR;
            } else {
                return false;
            }
        }

        if (! $recursive || ! $this->isDir($file)) {
            return @chmod($file, $mode);
        }
        // Is a directory, and we want recursive
        $file = Path::trailSlashAsReal($file);
        $file_list = $this->directoryList($file);
        if (is_array($file_list)) {
            foreach ($file_list as $filename => $meta_file) {
                $this->chMod($file . $filename, $mode, $recursive);
            }
        }

        return true;
    }

    /**
     * Changes file owner
     *
     * @param string $file      Path to the file.
     * @param mixed  $owner     A user name or number.
     * @param bool   $recursive Optional. If set True changes file owner recursively.
     *                          Default false.
     *
     * @return bool Returns true on success or false on failure.
     */
    public function chOwn($file, $owner, $recursive = false)
    {
        if (! $this->exists($file)) {
            return false;
        }
        if (! $recursive) {
            return @chown($file, $owner);
        }
        if (! $this->isDir($file)) {
            return @chown($file, $owner);
        }
        // Is a directory, and we want recursive
        $file_list = $this->directoryList($file);
        if (is_array($file_list)) {
            foreach ($file_list as $filename) {
                $this->chOwn($file . '/' . $filename, $owner, $recursive);
            }
        }
        return true;
    }

    /**
     * Gets file owner
     *
     * @param string $file Path to the file.
     *
     * @return string|bool Username of the user or false on error.
     */
    public function owner($file)
    {
        if (!function_exists('fileowner') || ! $this->exists($file)) {
            return false;
        }
        $owner_uid = @fileowner($file);
        if (! $owner_uid) {
            return false;
        }
        if (! function_exists('posix_getpwuid')) {
            return $owner_uid;
        }
        $owner_array = posix_getpwuid($owner_uid);
        return $owner_array['name'];
    }

    /**
     * Gets file permissions
     *
     * @access public
     *
     * @param string $file Path to the file.
     *
     * @return string Mode of the file (last 3 digits).
     */
    public function getChMod($file)
    {
        if (!$this->exists($file) || !function_exists('decoct') || ! function_exists('fileperms')) {
            return false;
        }
        return substr(decoct(@fileperms($file)), -3);
    }

    /**
     * Set Group
     *
     * @param string $file
     *
     * @return string|false
     */
    public function group($file)
    {
        if ($this->exists($file) || ! function_exists('filegroup')) {
            return false;
        }
        $gid = @filegroup($file);
        if (! $gid) {
            return false;
        }
        if (! function_exists('posix_getgrgid')) {
            return $gid;
        }
        $group_array = posix_getgrgid($gid);
        return $group_array['name'];
    }

    /**
     * Copy Existing Path
     *
     * @param string    $source
     * @param string    $destination
     * @param bool      $overwrite
     * @param int|bool  $mode
     *
     * @return bool
     */
    public function copy($source, $destination, $overwrite = false, $mode = false)
    {
        if (! $overwrite && $this->exists($destination)) {
            return false;
        }

        $ret_val = copy($source, $destination);
        if ($mode) {
            $this->chMod($destination, $mode);
        }
        return $ret_val;
    }

    /**
     * Move Paths
     *
     * @param string $source
     * @param string $destination
     * @param bool   $overwrite
     *
     * @return bool
     */
    public function move($source, $destination, $overwrite = false)
    {
        if (! $overwrite && $this->exists($destination)) {
            return false;
        }

        // Try using rename first. if that fails (for example, source is read only) try copy.
        if (@rename($source, $destination)) {
            return true;
        }

        if ($this->copy($source, $destination, $overwrite) && $this->exists($destination)) {
            $this->delete($source);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete File
     *
     * @param string      $file
     * @param bool        $recursive
     * @param string|bool $type
     *
     * @return bool
     */
    public function delete($file, $recursive = false, $type = false)
    {
        if (empty($file)) {
            // Some filesystems report this as /,
            // which can cause non-expected recursive deletion of all files in the filesystem.
            return false;
        }

        if ('f' == $type || $this->isFile($file)) {
            return @unlink($file);
        }
        if (! $recursive && $this->isFile($file)) {
            return @rmdir($file);
        }

        // At this point it's a folder, and we're in recursive mode
        $file = Path::trailSlashAsUnix($file);
        $file_list = $this->directoryList($file, true);

        $ret_val = true;
        if (is_array($file_list)) {
            foreach ($file_list as $filename => $file_info) {
                if (! $this->delete($file . $filename, $recursive, $file_info['type'])) {
                    $ret_val = false;
                }
            }
        }

        if ($this->exists($file) && ! @rmdir($file)) {
            $ret_val = false;
        }

        return $ret_val;
    }

    /**
     * Get file atime
     *
     * @param string $file
     *
     * @return int|bool
     */
    public function aTime($file)
    {
        if ($this->exists($file)) {
            return @fileatime($file);
        }
        return false;
    }

    /**
     * Get file mtime
     *
     * @param string $file
     *
     * @return int|bool
     */
    public function mTime($file)
    {
        if ($this->exists($file)) {
            return @filemtime($file);
        }
        return false;
    }

    /**
     * Get file size
     *
     * @param string $file
     *
     * @return int
     */
    public function size($file)
    {
        if ($this->exists($file)) {
            return @filesize($file);
        }
        return false;
    }

    /**
     * Create file
     *
     * @param string $file
     * @param int $time
     * @param int $a_time
     *
     * @return bool
     */
    public function touch($file, $time = 0, $a_time = 0)
    {
        if ($this->exists($file)) {
            return false;
        }

        if ($time == 0) {
            $time = time();
        }
        if ($a_time == 0) {
            $a_time = time();
        }
        return @touch($file, $time, $a_time);
    }

    /**
     * Create Directory
     *
     * @param string $path
     * @param mixed  $chmod
     * @param mixed  $ch_own
     * @param mixed  $ch_grep
     *
     * @return bool
     */
    public function mkDir($path, $chmod = false, $ch_own = false, $ch_grep = false)
    {
        // Safe mode fails with a trailing slash under certain PHP versions.
        $path = Path::unTrailSlashAsUnix($path);
        if (empty($path)) {
            return false;
        }
        if (! $chmod) {
            $chmod = self::CHMOD_DIR;
        }

        if (! @mkdir($path)) {
            return false;
        }
        $this->chMod($path, $chmod);
        if ($ch_own) {
            $this->chOwn($path, $ch_own);
        }
        if ($ch_grep) {
            $this->chGrp($path, $ch_grep);
        }
        return true;
    }

    /**
     * Remove Directory
     *
     * @param string $path
     * @param bool   $recursive
     * @return bool
     */
    public function rmDir($path, $recursive = false)
    {
        return $this->delete($path, $recursive);
    }

    /**
     * Directory Lists
     *
     * @param string $path
     * @param bool $include_hidden
     * @param bool $recursive
     * @return bool|array
     */
    public function directoryList($path, $include_hidden = true, $recursive = false)
    {
        if (!is_string($path)) {
            return false;
        }

        if ($this->isFile($path)) {
            $limit_file = basename($path);
            $path = dirname($path);
        } else {
            $limit_file = false;
        }

        if (! $this->isDir($path)) {
            return false;
        }

        $path = Path::unTrailSlashAsUnix($path);
        $dir = @dir($path);
        if (! $dir) {
            return false;
        }

        $ret = [];

        while (false !== ($entry = $dir->read())) {
            $structure = [];
            $structure['name'] = $entry;

            if ('.' == $structure['name'] || '..' == $structure['name']
                || ! $include_hidden && '.' == $structure['name'][0]
                ||  $limit_file && $structure['name'] != $limit_file
            ) {
                continue;
            }

            $structure['permission_h']  = $this->getHChMod($path.'/'.$entry);
            $structure['permission']    = $this->getNumFromHChMod($structure['permission_h']);
            $structure['number']        = false;
            $structure['owner']         = $this->owner($path.'/'.$entry);
            $structure['group']         = $this->group($path.'/'.$entry);
            $structure['size']          = $this->size($path.'/'.$entry);
            $structure['last_modification_unix']= $this->mTime($path.'/'.$entry);
            $structure['last_modification'] = @date('M j', $structure['last_modification_unix']);
            $structure['time']          = @date('h:i:s', $structure['last_modification_unix']);
            $structure['type']          = $this->isDir($path.'/'.$entry) ? 'd' : 'f';

            if ('d' == $structure['type']) {
                $structure['files'] = ! $recursive
                    ? []
                    : $this->directoryList($path . '/' . $structure['name'], $include_hidden, $recursive);
            }

            $ret[$structure['name']] = $structure;
        }

        $dir->close();
        unset($dir);
        return $ret;
    }
}
