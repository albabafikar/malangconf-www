<?php
/**
 * Logger Message Class
 *
 * @author  nawa <nawa@yahoo.com>
 * @version 1.0.0
 * @license MIT
 */
namespace MalangPhp\Site\Conf\Helper;

use MalangPhp\Site\Conf\Abstracts\LogMessageAbstract;

/**
 * Class LogMessage
 * @package MalangPhp\Site\Conf\Helper
 */
class LogMessage extends LogMessageAbstract implements \Serializable
{
    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     *
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize($this->traced);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized The string representation of the object.
     */
    public function unserialize($serialized)
    {
        $this->traced  = unserialize($serialized);
    }
}
