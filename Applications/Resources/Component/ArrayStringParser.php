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
class ArrayStringParser
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
}
