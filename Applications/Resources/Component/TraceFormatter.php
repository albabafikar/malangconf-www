<?php
/**
 * Trace Collection Formatter Component
 *
 * @author  nawa <nawa@yahoo.com>
 * @version 1.0.0
 * @license MIT
 */
namespace MalangPhp\Site\Conf\Component;

/**
 * Class TraceFormatter
 * @package MalangPhp\Site\Conf\Component
 */
class TraceFormatter
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * Formatter constructor.
     *
     * @param mixed $message
     */
    public function __construct($message)
    {
        $this->data = $message;
    }

    /***
     * @return string
     */
    public function getType()
    {
        return gettype($this->data);
    }

    /**
     * @param null|string $type
     *
     * @return bool|mixed|string
     */
    public function compileAs($type = null)
    {
        if (is_string($type)) {
            $type = strtolower($type);
        }

        if (!$type) {
            return $this->data;
        }

        switch ($type) {
            case 'bool':
            case 'boolean':
                return (bool) $this->data;
            case 'string':
                $type = $this->getType();
                if ($type == 'string') {
                    return $this->data;
                }
                if (empty($this->data)) {
                    return '';
                }
                if ($type == 'boolean') {
                    return '1';
                }
                if ($type === 'resource') {
                    return 'Resource of : '. get_resource_type($this->data);
                }
                if ($type === 'integer' || $type == 'double' || $type == 'float') {
                    return (string) $type;
                }
                if ($type === 'object') {
                    return print_r($this->data, true);
                }
                if ($type == 'array') {
                    return print_r($this->compileAs('array'), true);
                }
                return (string) $this->data;
            case 'array':
                $type = $this->getType();
                if ($type == 'array') {
                    return $this->data;
                }
                if (empty($this->data)) {
                    return [];
                }
                if ($type === 'object') {
                    return (array) $this->data;
                }
                return [$this->data];
            case 'object':
                $class = new \stdClass();
                if (empty($this->data)) {
                    return $class;
                }
                foreach ($this->compileAs('array') as $key => $value) {
                    $class->{$key} = $value;
                }
        }

        return $this->data;
    }

    /**
     * Loop string
     *
     * @param array $array
     * @param int $count
     *
     * @return string
     */
    protected function arrayLoopAsString(array $array, $count = 0)
    {
        $data = "#[array]\n";
        foreach ($array as $key => $value) {
            if (is_object($value)) {
                $value = (array) $value;
            }

            $sep = str_repeat('    ', $count+1) . '+';
            $data .= (is_array($value) ? "\n" . $sep . $this->arrayLoopAsString($value, $count+1) : $sep . $value);
        }

        return $data;
    }

    /**
     * @return string
     */
    public function asString()
    {
        return $this->compileAs('string');
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->compileAs('array');
    }

    /**
     * @return object
     */
    public function asObject()
    {
        return $this->compileAs('object');
    }

    /**
     * @return boolean
     */
    public function asBool()
    {
        return $this->compileAs('bool');
    }

    /**
     * @return boolean
     */
    public function asBoolean()
    {
        return $this->compileAs('bool');
    }

    /**
     * @return string
     */
    public function asJson()
    {
        return $this->getType() != 'resource' ? json_encode($this->data) : '{}';
    }

    /**
     * return string
     */
    public function asSerialize()
    {
        // resource could not be serialize, resource as string
        if ($this->getType() != 'resource') {
            return serialize('');
        }

        if ($this->getType() == 'object') {
            // closure could not being serialize
            if ($this->data instanceof \Closure) {
                return serialize(new \stdClass());
            }
            return serialize($this->asObject());
        }

        return serialize($this->data);
    }

    /**
     * As trace
     *
     * @return string
     */
    public function asStringTrace()
    {
        return $this->arrayLoopAsString($this->asArray());
    }
}
