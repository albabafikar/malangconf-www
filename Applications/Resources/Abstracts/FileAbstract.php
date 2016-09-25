<?php
namespace MalangPhp\Site\Conf\Abstracts;

use MalangPhp\Site\Conf\Helper\Path;

/**
 * Class FileAbstract
 * @package MalangPhp\Site\Conf\Abstracts
 */
abstract class FileAbstract
{
    /**
     * CHMOD FILE
     */
    const CHMOD_FILE = 0644;

    /**
     * CHMOD DIR
     */
    const CHMOD_DIR  = 0755;

    /**
     * CHMOD Allow all
     */
    const CHMOD_ALL = 0777;

    /**
     * @var boolean
     */
    public $verbose;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * Check if file / directory Exists
     *
     * @param string $path
     *
     * @return bool
     */
    abstract public function exists($path);

    /**
     * Check if file Exists and it is File
     *
     * @param string $path
     *
     * @return bool
     */
    abstract public function isFile($path);

    /**
     * Check if Directory exists and it is file
     *
     * @param string $dir
     *
     * @return bool
     */
    abstract public function isDir($dir);

    /**
     * @param string $path
     *
     * @return bool
     */
    abstract public function isReadable($path);

    /**
     * Check if Writable
     *
     * @param string $path
     *
     * @return bool
     */
    abstract public function isWritable($path);

    /**
     * Get Content
     *
     * @param string       $path
     * @param null|integer $length null if get All
     *
     * @return bool|string
     */
    abstract public function getContents($path, $length = null);

    /**
     * Get Content as Array
     *
     * @param string $path
     *
     * @return array|bool
     */
    abstract public function getContentsAsArray($path);

    /**
     * Write content into file
     *
     * @param string    $path
     * @param string    $data
     * @param int|bool  $mode Optional. The file permissions as octal number, usually 0644.
     *
     * @return bool
     */
    abstract public function writeContent($path, $data, $mode = false);

    /**
     * Gets the current working directory
     *
     * @return string|bool the current working directory on success, or false on failure.
     */
    abstract public function cwd();

    /**
     * Change directory
     *
     * @param string $dir The new current directory.
     *
     * @return bool Returns true on success or false on failure.
     */
    abstract public function chDir($dir);

    /**
     * Changes file group
     *
     * @param string $file      Path to the file.
     * @param mixed  $group     A group name or number.
     * @param bool   $recursive Optional. If set True changes file group recursively. Default false.
     *
     * @return bool Returns true on success or false on failure.
     */
    abstract public function chGrp($file, $group, $recursive = false);

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
    abstract public function chMod($file, $mode = false, $recursive = false);

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
    abstract public function chOwn($file, $owner, $recursive = false);

    /**
     * Gets file owner
     *
     * @param string $file Path to the file.
     *
     * @return string|bool Username of the user or false on error.
     */
    abstract public function owner($file);

    /**
     * Gets file permissions
     *
     * @access public
     *
     * @param string $file Path to the file.
     *
     * @return string Mode of the file (last 3 digits).
     */
    abstract public function getChMod($file);

    /**
     * Set Group
     *
     * @param string $file
     *
     * @return string|false
     */
    abstract public function group($file);

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
    abstract public function copy($source, $destination, $overwrite = false, $mode = false);

    /**
     * Move Paths
     *
     * @param string $source
     * @param string $destination
     * @param bool   $overwrite
     *
     * @return bool
     */
    abstract public function move($source, $destination, $overwrite = false);

    /**
     * Delete File
     *
     * @param string      $file
     * @param bool        $recursive
     * @param string|bool $type
     *
     * @return bool
     */
    abstract public function delete($file, $recursive = false, $type = false);

    /**
     * Get file atime
     *
     * @param string $file
     *
     * @return int
     */
    abstract public function aTime($file);

    /**
     * Get file mtime
     *
     * @param string $file
     *
     * @return int
     */
    abstract public function mTime($file);

    /**
     * Get file size
     *
     * @param string $file
     *
     * @return int
     */
    abstract public function size($file);

    /**
     * Create file
     *
     * @param string $file
     * @param int $time
     * @param int $atime
     *
     * @return bool
     */
    abstract public function touch($file, $time = 0, $atime = 0);

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
    abstract public function mkDir($path, $chmod = false, $ch_own = false, $ch_grep = false);

    /**
     * Remove Directory
     *
     * @param string $path
     * @param bool $recursive
     * @return bool
     */
    abstract public function rmDir($path, $recursive = false);

    /**
     * Directory Lists
     *
     * @param string $path
     * @param bool $include_hidden
     * @param bool $recursive
     * @return bool|array
     */
    abstract public function directoryList($path, $include_hidden = true, $recursive = false);

    /**
     * Return the *nix-style file permissions for a file.
     *
     * From the PHP documentation page for filePerms().
     *
     * @link http://docs.php.net/fileperms
     * @final
     *
     * @param string $file String filename.
     *
     * @return string The *nix-style representation of permissions.
     */
    final public function getHChMod($file)
    {
        $perms = intval($this->getChMod($file), 8);
        if (($perms & 0xC000) == 0xC000) { // Socket
            $info = 's';
        } elseif (($perms & 0xA000) == 0xA000) { // Symbolic Link
            $info = 'l';
        } elseif (($perms & 0x8000) == 0x8000) { // Regular
            $info = '-';
        } elseif (($perms & 0x6000) == 0x6000) { // Block special
            $info = 'b';
        } elseif (($perms & 0x4000) == 0x4000) { // Directory
            $info = 'd';
        } elseif (($perms & 0x2000) == 0x2000) { // Character special
            $info = 'c';
        } elseif (($perms & 0x1000) == 0x1000) { // FIFO pipe
            $info = 'p';
        } else { // Unknown
            $info = 'u';
        }

        // Owner
        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ?
            (($perms & 0x0800) ? 's' : 'x') :
            (($perms & 0x0800) ? 'S' : '-'));

        // Group
        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ?
            (($perms & 0x0400) ? 's' : 'x') :
            (($perms & 0x0400) ? 'S' : '-'));

        // World
        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ?
            (($perms & 0x0200) ? 't' : 'x') :
            (($perms & 0x0200) ? 'T' : '-'));

        return $info;
    }

    /**
     * Convert *nix-style file permissions to a octal number.
     *
     * Converts '-rw-r--r--' to 0644
     * From "info at rvgate dot nl"'s comment on the PHP documentation for chmod()
     *
     * @link http://docs.php.net/manual/en/function.chmod.php#49614
     * @final
     *
     * @param string $mode string The *nix-style file permission.
     *
     * @return int octal representation
     */
    final public function getNumFromHChMod($mode)
    {
        $real_mode = '';
        $legals = ['', 'w', 'r', 'x', '-'];
        $att_array = preg_split('//', $mode);

        for ($i = 0, $c = count($att_array); $i < $c; $i++) {
            if ($key = array_search($att_array[$i], $legals)) {
                $real_mode .= $legals[$key];
            }
        }

        $mode = str_pad($real_mode, 10, '-', STR_PAD_LEFT);
        $trans = [
            '-' => '0',
            'r' => '4',
            'w' => '2',
            'x' => '1'
        ];
        $mode = strtr($mode, $trans);

        $new_mode = $mode[0];
        $new_mode .= $mode[1] + $mode[2] + $mode[3];
        $new_mode .= $mode[4] + $mode[5] + $mode[6];
        $new_mode .= $mode[7] + $mode[8] + $mode[9];

        return $new_mode;
    }

    /**
     * Determine if the string provided contains binary characters.
     *
     * @param string $text String to test against.
     *
     * @return bool true if string is binary, false otherwise.
     */
    public function isBinaryString($text)
    {
        return is_string($text) && preg_match('|[^\x20-\x7E]|', $text); // chr(32)..chr(127)
    }

    /**
     * Connect filesystem.
     *
     * @abstract
     *
     * @return bool True on success or false on failure (always true for WP_Filesystem_Direct).
     */
    public function connect()
    {
        return true;
    }

    /**
     * Locate a folder on the remote filesystem.
     *
     * Assumes that on Windows systems, Stripping off the Drive
     * letter is OK Sanitizes \\ to / in windows filepaths.
     *
     * @param string $folder the folder to locate.
     *
     * @return string|false The location of the remote path, false on failure.
     */
    public function findFolder($folder)
    {
        if (isset($this->cache[$folder])) {
            return $this->cache[$folder];
        }

        if (stripos($this->mode, 'ftp') !== false) {
            $constant_overrides = [
                'FTP_ROOT'              => dirname($_SERVER['SCRIPT_FILENAME']),
                'FTP_RESOURCE_PATH'     => dirname(__DIR__),
                'FTP_APPLICATION_PATH'  => dirname(dirname(__DIR__))
            ];

            // Direct matches ( folder = CONSTANT/ )
            foreach ($constant_overrides as $constant => $dir) {
                if (! defined($constant)) {
                    continue;
                }
                if ($folder === $dir) {
                    return Path::trailSlashAsUnix(constant($constant));
                }
            }

            // Prefix Matches ( folder = CONSTANT/subdir )
            foreach ($constant_overrides as $constant => $dir) {
                if (! defined($constant)) {
                    continue;
                }
                if (0 === stripos($folder, $dir)) { // $folder starts with $dir
                    $potential_folder = preg_replace(
                        '#^' . preg_quote($dir, '#') . '/#i',
                        Path::trailSlashAsUnix(constant($constant)),
                        $folder
                    );
                    $potential_folder = Path::trailSlashAsUnix($potential_folder);
                    if ($this->isDir($potential_folder)) {
                        $this->cache[$folder] = $potential_folder;
                        return $potential_folder;
                    }
                }
            }
        } elseif ('direct' == strtolower($this->mode)) {
            $folder = str_replace('\\', '/', $folder); // Windows path sanitisation
            return Path::trailSlashAsUnix($folder);
        }

        $folder = preg_replace('|^([a-z]{1}):|i', '', $folder); // Strip out windows drive letter if it's there.
        $folder = str_replace('\\', '/', $folder); // Windows path sanitisation

        if (isset($this->cache[$folder])) {
            return $this->cache[$folder];
        }

        if ($this->exists($folder)) { // Folder exists at that absolute path.
            $folder = Path::trailSlashAsUnix($folder);
            $this->cache[$folder] = $folder;
            return $folder;
        }
        if ($return = $this->searchForFolder($folder)) {
            $this->cache[$folder] = $return;
        }
        return $return;
    }

    /**
     * Locate a folder on the remote filesystem.
     *
     * Expects Windows sanitized path.
     *
     * @param string $folder The folder to locate.
     * @param string $base   The folder to start searching from.
     * @param bool   $loop   If the function has recursed, Internal use only.
     * @return string|false  The location of the remote path, false to cease looping.
     */
    public function searchForFolder($folder, $base = '.', $loop = false)
    {
        if (empty($base) || '.' == $base) {
            $base = Path::trailSlashAsUnix($this->cwd());
        }

        $folder = Path::unTrailSlashAsUnix($folder);

        if ($this->verbose) {
            /* translators: 1: folder to locate, 2: folder to start searching from */
            printf("\n" . ('Looking for %1$s in %2$s') . "<br/>\n", $folder, $base);
        }

        $folder_parts = explode('/', $folder);
        $folder_part_keys = array_keys($folder_parts);
        $last_index = array_pop($folder_part_keys);
        $last_path = $folder_parts[ $last_index ];

        $files = $this->directoryList($base);
        if (!is_array($files)) {
            return false;
        }
        foreach ($folder_parts as $index => $key) {
            if ($index == $last_index) {
                continue; // We want this to be caught by the next code block.
            }

            /*
             * Working from /home/ to /user/ to /wordpress/ see if that file exists within
             * the current folder, If it's found, change into it and follow through looking
             * for it. If it cant find WordPress down that route, it'll continue onto the next
             * folder level, and see if that matches, and so on. If it reaches the end, and still
             * cant find it, it'll return false for the entire function.
             */
            if (isset($files[$key])) {
                // Let's try that folder:
                $new_dir = Path::trailSlashAsUnix(Path::join($base, $key));
                if ($this->verbose) {
                    /* translators: %s: directory name */
                    printf("\n" . ('Changing to %s') . "<br/>\n", $new_dir);
                }

                // Only search for the remaining path tokens in the directory, not the full path again.
                $new_folder = implode('/', array_slice($folder_parts, $index + 1));
                if ($ret = $this->searchForFolder($new_folder, $new_dir, $loop)) {
                    return $ret;
                }
            }
        }

        // Only check this as a last resort, to prevent locating the incorrect install.
        // All above procedures will fail quickly if this is the right branch to take.
        if (isset($files[$last_path])) {
            if ($this->verbose) {
                printf("\n" . ('Found %s') . "<br/>\n", $base . $last_path);
            }
            return Path::trailSlashAsUnix($base . $last_path);
        }

        // Prevent this function from looping again.
        // No need to proceed if we've just searched in /
        if ($loop || '/' == $base) {
            return false;
        }

        // As an extra last resort, Change back to / if the folder wasn't found.
        // This comes into effect when the CWD is /home/user/ but WP is at /var/www/....
        return $this->searchForFolder($folder, '/', true);
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }
}
