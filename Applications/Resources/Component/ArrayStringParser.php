<?php
/**
 * Array String Parser API Save data Array to make it callable via
 *      string or array multi dimensional
 *
 * purpose to @uses \MalangPhp\Site\Conf\Component\InputHttp
 *
 * @author  nawa <nawa@yahoo.com>
 * @version 1.0.0
 * @license MIT
 */

namespace MalangPhp\Site\Conf\Component;

use MalangPhp\Site\Conf\Traits\ArrayBracketResolver;
use Slim\Collection;

/**
 * Class ArrayStringParser
 * @package MalangPhp\Site\Conf\Component
 */
class ArrayStringParser implements \ArrayAccess
{
    use ArrayBracketResolver;

    /**
     * Get collector
     *
     * @return array
     */
    public function data()
    {
        return $this->getCollection();
    }

    /**
     * Check if has key
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->getDataCollection()->has($name);
    }

    /**
     * Get collector object
     *
     * @return Collection
     */
    public function getDataCollection()
    {
        /**
         * Create new Collector Object
         */
        return new Collection($this->getCollection());
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset An offset to check for.
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset The offset to retrieve.
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->fetch($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->collection->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset The offset to unset.
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->collection->remove($offset);
    }
}
