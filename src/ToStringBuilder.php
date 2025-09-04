<?php
namespace MawebDK\ToStringBuilder;

use Stringable;
use UnitEnum;

/**
 * Builder class for __toString() methods.
 *
 * Sample usage:
 *   public function __toString(): string
 *   {
 *       $toStringBuilder = new ToStringBuilder(object: $this);
 *
 *       return $toStringBuilder
 *           ->add('firstname', $this->firstname)
 *           ->add('lastname', $this->lastname)
 *           ->add('age', $this->age)
 *           ->build();
 *   }
 *
 * Sample output:
 *   Namespace\Classname{"firstname": "John", "lastname": "Doe", "age": 18}
 */
class ToStringBuilder
{
    /**
     * Assignment separator.
     */
    private const string ASSIGNMENT_SEPARATOR = ': ';

    /**
     * Values separator.
     */
    private const string VALUES_SEPARATOR = ', ';

    /**
     * @var string   Classname.
     */
    private string $classname;

    /**
     * @var array   Values.
     */
    private array $values = [];

    /**
     * Format a value to string depending on the datatype.
     * @param mixed $value   Value to be formatted as a string.
     * @return string        Value formatted as a string.
     */
    public static function formatValue(mixed $value): string
    {
        if (is_null($value)):
            return 'null';
        elseif (is_bool($value)):
            return $value ? 'true' : 'false';
        elseif (is_int($value)):
            return (string)$value;
        elseif (is_float($value)):
            return (string)$value;
        elseif (is_string($value)):
            return '"' . $value . '"';
        elseif (is_array($value)):
            return self::formatArrayValue($value);
        elseif (is_object($value)):
            return self::formatObjectValue($value);
        elseif (is_resource($value)):
            return sprintf('resource(%s)', get_resource_type($value));
        else:
            return sprintf('%s{?}', gettype($value));
        endif;
    }

    /**
     * Constructor saves classname of the object.
     * @param object $object   Object.
     */
    public function __construct(object $object)
    {
        $this->classname = get_class(object: $object);
    }

    /**
     * Add an element to be used in the generated toString() output.
     * @param string $name   Name of the element.
     * @param mixed $value   Value of the element.
     * @return $this         The object itself for method chaining.
     */
    public function add(string $name, mixed $value): self
    {
        $this->values[] = sprintf('%s%s%s', self::formatValue($name), self::ASSIGNMENT_SEPARATOR, self::formatValue($value));

        return $this;
    }

    /**
     * Build output.
     * @return string   Built output.
     */
    public function build(): string
    {
        return sprintf('%s{%s}', $this->classname, implode(separator: self::VALUES_SEPARATOR, array: $this->values));
    }

    /**
     * Returns an array value as a string.
     * @param array $array   Array value.
     * @return string        Array value as a string.
     */
    private static function formatArrayValue(array $array): string
    {
        if (array_is_list(array: $array)):
            return self::formatArrayListValue(array: $array);
        else:
            return self::formatArrayMapValue(array: $array);
        endif;
    }

    /**
     * Returns an array (list) value as a string.
     * @param array $array   Array (list) value.
     * @return string        Array (list) value as a string.
     */
    private static function formatArrayListValue(array $array): string
    {
        $callback             = function(mixed $value): string { return self::formatValue($value); };
        $formattedArrayValues = array_map($callback, array: array_values(array: $array));

        return sprintf('[%s]', implode(separator: self::VALUES_SEPARATOR, array: $formattedArrayValues));
    }

    /**
     * Returns an array (map) value as a string.
     * @param array $array   Array (map) value.
     * @return string        Array (map) value as a string.
     */
    private static function formatArrayMapValue(array $array): string
    {
        $callback             = function(string $key, mixed $value): string {
            return sprintf('%s%s%s', self::formatValue($key), self::ASSIGNMENT_SEPARATOR, self::formatValue($value));
        };
        $formattedArrayValues = array_map($callback, array_keys(array: $array), array_values(array: $array));

        return sprintf('[%s]', implode(separator: self::VALUES_SEPARATOR, array: $formattedArrayValues));
    }

    /**
     * Returns an object value as a string.
     * @param object $object   Object value.
     * @return string          Object value as a string.
     */
    private static function formatObjectValue(object $object): string
    {
        if ($object instanceof Stringable):
            return (string)$object;
        elseif ($object instanceof UnitEnum):
            return sprintf('%s::%s', $object::class, $object->name);
        else:
            return sprintf('%s{?}', $object::class);
        endif;
    }
}