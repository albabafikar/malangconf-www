<?php
/**
 * Array Bracket Resolver Parser Trait API
 *      Save data Array to make it callable via
 *      string or array multi dimensional
 *
 * purpose to @uses \MalangPhp\Site\Conf\Component\ArrayStringParser
 *
 * @author  nawa <nawa@yahoo.com>
 * @version 1.0.0
 * @license MIT
 */
namespace MalangPhp\Site\Conf\Traits;

use Slim\Collection;

/**
 * Trait ArrayBracketResolver
 * @package MalangPhp\Site\Conf\Traits
 */
trait ArrayBracketResolver
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * ArrayBracketResolver constructor.
     *
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        // set data
        $this->setCollection($args);
    }

    /**
     * @param array $args
     */
    public function setCollection(array $args = [])
    {
        $this->collection = new Collection($args);
    }

    /**
     * @return array
     */
    public function getCollection()
    {
        return $this->collection->all();
    }

    /**
     * Fetch from array
     * Internal method used to retrieve values from global arrays.
     * alias of fetchFrom[];
     *
     * @param   mixed   $index   Index for item to be fetched from $array
     * @param   mixed   $default Default return if not exist
     * @return  mixed
     */
    public function fetch($index = null, $default = null)
    {
        return $this->fetchFromArray($index, $default);
    }

    /**
     * Fetch from array
     * Internal method used to retrieve values from global arrays.
     * alias of fetchFrom[];
     *
     * @param   mixed   $index   Index for item to be fetched from $array
     * @param   mixed   $default Default return if not exist
     * @return  mixed
     */
    public function get($index, $default = null)
    {
        return $this->fetchFromArray($index, $default);
    }

    /**
     * Fetch from array
     *
     * Internal method used to retrieve values from global arrays.
     *
     * @param   mixed   $index   Index for item to be fetched from $array
     * @param   mixed   $default Default return if not exist
     * @return  mixed
     */
    protected function fetchFromArray($index = null, $default = null)
    {
        $array = $this->getCollection();
        // If $index is NULL, it means that the whole $array is requested
        isset($index) || $index = array_keys($array);
        // allow fetching multiple keys at once
        if (is_array($index)) {
            $output = [];
            foreach ($index as $key) {
                $output[$key] = $this->fetchFromArray($key);
            }
            return $output;
        }
        if (isset($array[$index])) {
            $value = $array[$index];
        } elseif (($count = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $index, $matches)) > 1) {
            // Does the index contain array notation
            $value = $array;
            for ($i = 0; $i < $count; $i++) {
                $key = trim($matches[0][$i], '[]');
                // Empty notation will return the value as array
                if ($key === '') {
                    break;
                }
                if (isset($value[$key])) {
                    $value = $value[$key];
                } else {
                    return $default;
                }
            }
        } else {
            return $default;
        }

        return $value;
    }
}
