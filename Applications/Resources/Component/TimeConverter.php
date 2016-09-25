<?php
/**
 * Time Convert
 *
 * @author  nawa <nawa@yahoo.com>
 * @version 1.0.0
 * @license MIT
 */
namespace MalangPhp\Site\Conf\Component;

/**
 * Class TimeConvert
 * @package MalangPhp\Site\Conf\Component
 */
class TimeConverter
{
    /**
     * @var mixed
     */
    protected $microTime;

    /**
     * @var int
     */
    protected $time;

    /**
     * @var int
     */
    protected $gmt_time;

    /**
     * @var string
     */
    protected $gmt_time_db;

    /**
     * @var int
     */
    protected $set_time;

    /**
     * @var int
     */
    protected $set_gmt_time;

    /**
     * @var string
     */
    protected $set_gmt_time_db;

    /**
     * @var string
     */
    protected $timezone;

    /**
     * Time constructor.
     * @param mixed $time
     */
    public function __construct($time = null)
    {
        if (!ini_get('date.timezone')) {
            // fix error notices
            $time_zone = @date_default_timezone_get()?: 'UTC';
            ini_set('date.timezone', $time_zone);
            date_default_timezone_set($time_zone);
        }

        $this->timezone = date_default_timezone_get();
        $this->microTime = microtime();
        $this->time = time();
        $this->set_time = $this->time;
        $this->gmt_time_db = gmdate('Y-m-d H:i:s', $this->set_time);
        $this->gmt_time = strtotime($this->gmt_time_db);
        $this->set_gmt_time_db = $this->gmt_time_db;
        $this->set_gmt_time = $this->gmt_time;

        if ($time && ! is_int($time)) {
            $this->set_time = strtotime($time);
            $this->set_gmt_time_db = gmdate('Y-m-d H:i:s', $this->set_time);
            $this->set_gmt_time = strtotime($this->set_gmt_time_db);
        } elseif (is_int($time)) {
            $this->set_time = $time > strtotime('1970') ? $time : $this->time + $time;
            $this->set_gmt_time_db = gmdate('Y-m-d H:i:s', $this->set_time);
            $this->set_gmt_time = strtotime($this->set_gmt_time_db);
        }
    }

    /**
     * @param string $format
     * @return bool|string
     */
    public function format($format)
    {
        return date($format, $this->set_time);
    }

    /**
     * Get offset
     *
     * @return int in seconds
     */
    public function getOffset()
    {
        return ($this->gmt_time - $this->set_time);
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return int
     */
    public function getCurrentTime()
    {
        return $this->set_time;
    }

    /**
     * @return int
     */
    public function getCurrentUTCTime()
    {
        return $this->gmt_time;
    }

    /**
     * @return int
     */
    public function toUTCTime()
    {
        return $this->set_gmt_time;
    }

    /**
     * @param string $format
     * @return bool|int|string
     */
    public function toUTC($format = 'Y-m-d H:i:s')
    {
        if (!$format) {
            return $this->set_gmt_time;
        }
        return date($format, $this->set_gmt_time);
    }

    /**
     * @param null|string $format
     * @return bool|int|string
     */
    public function currentToUTC($format = 'Y-m-d H:i:s')
    {
        if (!$format) {
            return $this->gmt_time;
        }
        return date($format, $this->gmt_time);
    }

    /**
     * @return string
     */
    public function toDBTime()
    {
        return $this->set_gmt_time_db;
    }

    /**
     * @return string
     */
    public function currentToDBTime()
    {
        return $this->gmt_time_db;
    }

    /**
     * @param mixed $time
     * @return TimeConverter
     */
    public static function create($time)
    {
        return new TimeConverter($time);
    }
}
