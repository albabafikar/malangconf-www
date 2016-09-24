<?php
/**
 * Benchmark Collection -> contains memory & time elapsed values
 *
 * @author  nawa <nawa@yahoo.com>
 * @version 1.0.0
 * @license MIT
 */
namespace MalangPhp\Site\Conf\Component;

use Slim\Collection;

/**
 * Class BenchMark
 * @package MalangPhp\Site\Conf\Component
 */
class BenchMark
{
    /**
     * @var Collection[]
     */
    protected $collection;

    /**
     * @var Collection[]
     */
    protected $old_collect;

    /**
     * BenchMark constructor.
     */
    public function __construct()
    {
        $this->collection = new Collection();
        $this->old_collect = new Collection();
    }

    /**
     * @return Collection
     */
    protected function buildProperty()
    {
        return new Collection([
            'memory'      => memory_get_usage(),
            'memory_real' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'microtime'   => microtime(true),
            'microtime_string' => microtime(),
        ]);
    }

    /**
     * @param string $name
     */
    public function start($name)
    {
        if (!is_string($name)) {
            return;
        }
        if (!$this->has($name)) {
            $this->collection->set($name, $this->buildProperty());
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->collection->has($name);
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return $this->collection->get($name, $default);
    }

    /**
     * @param string $name
     * @return Collection
     */
    public function current($name = null)
    {
        $ret_val = new Collection();
        if ($name === null) {
            foreach ($this->collection->all() as $key => $v) {
                $ret_val->set(
                    $key,
                    new Collection([
                        'time_start'   => $v['microtime'],
                        'time_elapsed' => (microtime(true)    - $v['microtime']),
                        'memory_start' => $v['memory'],
                        'memory'       => (memory_get_usage() - $v['memory']),
                        'memory_real_start' => $v['memory_real'],
                        'memory_real'  => (memory_get_usage(true) - $v['memory_real']),
                        'memory_peak_start'=> $v['memory_peak'],
                        'memory_peak'  => (memory_get_peak_usage(true) - $v['memory_peak']),
                    ])
                );
                if ($this->old_collect->has($key)) {
                    $this->old_collect->set($key, $ret_val->get($key));
                }
            }
            return $ret_val;
        }

        if (is_string($name) && $this->collection->has($name)) {
            $v = $this->collection->get($name);
            $ret_val->replace(
                [
                    'time_start'   => $v['microtime'],
                    'time_elapsed' => (microtime(true)    - $v['microtime']),
                    'memory_start' => $v['memory'],
                    'memory'       => (memory_get_usage() - $v['memory']),
                    'memory_real_start' => $v['memory_real'],
                    'memory_real'  => (memory_get_usage(true) - $v['memory_real']),
                    'memory_peak_start'=> $v['memory_peak'],
                    'memory_peak'  => (memory_get_peak_usage(true) - $v['memory_peak']),
                ]
            );
            if (!$this->old_collect->has($name)) {
                $this->old_collect->set($name, $ret_val);
            }
        }

        return $ret_val;
    }

    /**
     * @param string $name
     * @return Collection
     */
    public function getBenchMarkOf($name = null)
    {
        if ($name === null) {
            foreach ($this->collection->all() as $key => $v) {
                if (!$this->old_collect->has($key)) {
                    $this->old_collect->set(
                        $key,
                        new Collection([
                            'time_start' => $v['microtime'],
                            'time_elapsed' => (microtime(true) - $v['microtime']),
                            'memory_start' => $v['memory'],
                            'memory' => (memory_get_usage() - $v['memory']),
                            'memory_real_start' => $v['memory_real'],
                            'memory_real' => (memory_get_usage(true) - $v['memory_real']),
                            'memory_peak_start' => $v['memory_peak'],
                            'memory_peak' => (memory_get_peak_usage(true) - $v['memory_peak']),
                        ])
                    );
                }
            }
            return $this->old_collect;
        }

        if (is_string($name) && $this->collection->has($name)) {
            $v = $this->collection->get($name);
            if (!$this->old_collect->has($name)) {
                $this->old_collect->set(
                    $name,
                    new Collection([
                        'time_start' => $v['microtime'],
                        'time_elapsed' => (microtime(true) - $v['microtime']),
                        'memory_start' => $v['memory'],
                        'memory' => (memory_get_usage() - $v['memory']),
                        'memory_real_start' => $v['memory_real'],
                        'memory_real' => (memory_get_usage(true) - $v['memory_real']),
                        'memory_peak_start' => $v['memory_peak'],
                        'memory_peak' => (memory_get_peak_usage(true) - $v['memory_peak']),
                    ])
                );
            }

            return $this->old_collect;
        }

        return new Collection();
    }
}
