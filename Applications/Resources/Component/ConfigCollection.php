<?php
/**
 * Config Collections
 *
 * @author  nawa <nawa@yahoo.com>
 * @version 1.0.0
 * @license MIT
 */
namespace MalangPhp\Site\Conf\Component;

use Slim\Collection;

/**
 * Class ConfigCollection
 * @package MalangPhp\Site\Conf\Component
 */
class ConfigCollection implements \Countable
{
    /**
     * @var Collection(ArrayStringParser[])
     */
    protected $configCollection;

    /**
     * Config constructor.
     * @param array $configArray
     * @throws \ErrorException
     */
    public function __construct(array $configArray = [])
    {
        if (!isset($this->configCollection)) {
            $this->configCollection = new Collection();
        }

        foreach ($configArray as $environment => $arrayConfig) {
            if (!is_array($arrayConfig)) {
                throw new \ErrorException(
                    sprintf(
                        'Invalid configuration data type for %s',
                        $environment
                    ),
                    E_USER_ERROR
                );
            }

            $this->set($environment, $arrayConfig);
        }
    }

    /**
     * Add Configuration
     *
     * @param string $environment environment
     * @param array  $configArray config array collection
     */
    public function set($environment, array $configArray)
    {
        $this->configCollection->set($environment, new ArrayStringParser($configArray));
    }

    /**
     * Getting Config from Environment
     *
     * @param string $environment
     * @return ArrayStringParser
     */
    public function get($environment)
    {
        return $this->configCollection->get($environment);
    }

    /**
     * Set Config
     *
     * @param string $environment
     */
    public function remove($environment)
    {
        $this->configCollection->remove($environment);
    }

    /**
     * Getting Array Keys of Collection
     *
     * @return array
     */
    public function keys()
    {
        return $this->configCollection->keys();
    }

    /**
     * Check if Config array contains key name
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->configCollection->has($name);
    }

    /**
     * Count Collection
     *
     * @return int
     */
    public function count()
    {
        return $this->configCollection->count();
    }

    /**
     * Getting All data Config
     *
     * @return ArrayStringParser[]
     */
    public function all()
    {
        return $this->configCollection->all();
    }
}
