<?php
namespace MawebDK\ToStringBuilder\Test;

use DateTime;
use MawebDK\ToStringBuilder\ToStringBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Stringable;

class ToStringBuilderTest extends TestCase
{
    #[DataProvider('dataProviderFormatValue')]
    public function testFormatValue(mixed $value, string $expectedFormattedValue)
    {
        $this->assertSame(
            expected: $expectedFormattedValue,
            actual: ToStringBuilder::formatValue($value)
        );
    }

    public static function dataProviderFormatValue(): array
    {
        return [
            'null' => [
                'value'                  => null,
                'expectedFormattedValue' => 'null',
            ],

            'Boolean false' => [
                'value'                  => false,
                'expectedFormattedValue' => 'false',
            ],
            'Boolean true' => [
                'value'                  => true,
                'expectedFormattedValue' => 'true',
            ],

            'Integer negative' => [
                'value'                  => -123456789,
                'expectedFormattedValue' => '-123456789',
            ],
            'Integer Positive' => [
                'value'                  => 123456789,
                'expectedFormattedValue' => '123456789',
            ],

            'Double -123456789.99' => [
                'value'                  => -123456789.99,
                'expectedFormattedValue' => '-123456789.99',
            ],
            'Double 123456789.99' => [
                'value'                  => 123456789.99,
                'expectedFormattedValue' => '123456789.99',
            ],
            'Double -1.23456789e-20' => [
                'value'                  => -1.23456789e-20,
                'expectedFormattedValue' => '-1.23456789E-20',
            ],
            'Double 1.23456789e-20' => [
                'value'                  => 1.23456789e-20,
                'expectedFormattedValue' => '1.23456789E-20',
            ],
            'Double -1.23456789e+20' => [
                'value'                  => -1.23456789e+20,
                'expectedFormattedValue' => '-1.23456789E+20',
            ],
            'Double 1.23456789e+20' => [
                'value'                  => 1.23456789e+20,
                'expectedFormattedValue' => '1.23456789E+20',
            ],

            'String empty' => [
                'value'                  => '',
                'expectedFormattedValue' => '""',
            ],
            'String "Hello World"' => [
                'value'                  => 'Hello World',
                'expectedFormattedValue' => '"Hello World"',
            ],
            'String with newline' => [
                'value'                  => 'Hello' . PHP_EOL . 'World',
                'expectedFormattedValue' => '"Hello' . PHP_EOL . 'World"',
            ],
            'String with numeric value' => [
                'value'                  => '123',
                'expectedFormattedValue' => '"123"',
            ],

            'Empty array' => [
                'value'                  => [],
                'expectedFormattedValue' => '[]',
            ],
            'List array' => [
                'value'                  => ['John', 'Doe', 18],
                'expectedFormattedValue' => '["John", "Doe", 18]',
            ],
            'Key/value array' => [
                'value'                  => ['firstname' => 'John', 'lastname' => 'Doe', 'age' => 18],
                'expectedFormattedValue' => '["firstname": "John", "lastname": "Doe", "age": 18]',
            ],
            'Multidimensional list array' => [
                'value'                  => ['John', 'Doe', [10, 20, 30]],
                'expectedFormattedValue' => '["John", "Doe", [10, 20, 30]]',
            ],
            'Multidimensional key/value array' => [
                'value'                  => ['name' => 'John Doe', 'age' => 18, 'father' => ['name' => 'John Doe Sr.']],
                'expectedFormattedValue' => '["name": "John Doe", "age": 18, "father": ["name": "John Doe Sr."]]',
            ],

            'Object implementing Stringable' => [
                'value'                  => new Test_ImplementStringable(),
                'expectedFormattedValue' => 'Test_ImplementStringable{"name": "John Doe", "age": 18}',
            ],
            'Object not implementing Stringable' => [
                'value'                  => new Test_NotImplementStringable(),
                'expectedFormattedValue' => 'MawebDK\ToStringBuilder\Test\Test_NotImplementStringable{?}',
            ],
            'Basic enumeration' => [
                'value'                  => Test_BasicEnum::ONE,
                'expectedFormattedValue' => 'MawebDK\ToStringBuilder\Test\Test_BasicEnum{?}',
            ],
            'Backed enumeration' => [
                'value'                  => Test_BackedEnum::ONE,
                'expectedFormattedValue' => 'MawebDK\ToStringBuilder\Test\Test_BackedEnum{?}',
            ],
            'Anonymous function' => [
                'value'                  => function(): string { return ''; },
                'expectedFormattedValue' => 'Closure{?}',
            ],

            'Stream resource' => [
                'value'                  => fopen(filename: 'php://stdout', mode: 'w'),
                'expectedFormattedValue' => 'resource(stream)',
            ],
        ];
    }

    #[DataProvider('dataProviderBuild')]
    public function testBuild(object $object, array $values, string $expectedToStringOutput)
    {
        $toStringBuilder = new ToStringBuilder(object: $object);

        foreach ($values as $name => $value):
            $toStringBuilder->add(name: $name, value: $value);
        endforeach;

        $this->assertSame(
            expected: $expectedToStringOutput,
            actual: $toStringBuilder->build()
        );
    }

    public static function dataProviderBuild(): array
    {
        return [
            'DateTime (root namespace)' => [
                'object'                 => DateTime::createFromFormat(format: 'Y-m-d', datetime: '1999-12-31'),
                'values'                 => [],
                'expectedToStringOutput' => 'DateTime{}'
            ],
            'Test_SimpleClass' => [
                'object'                 => new Test_SimpleClass(),
                'values'                 => [],
                'expectedToStringOutput' => 'MawebDK\\ToStringBuilder\\Test\\Test_SimpleClass{}'
            ],
            'Test_SimpleSubclass' => [
                'object'                 => new Test_SimpleSubclass(),
                'values'                 => [],
                'expectedToStringOutput' => 'MawebDK\\ToStringBuilder\\Test\\Test_SimpleSubclass{}'
            ],
            'Null value' => [
                'object'                 => new DateTime(),
                'values'                 => [
                    'null' => null,
                ],
                'expectedToStringOutput' => 'DateTime{"null": null}'
            ],
            'Boolean values' => [
                'object'                 => new DateTime(),
                'values'                 => [
                    'booleanFalse' => false,
                    'booleanTrue'  => true,
                ],
                'expectedToStringOutput' => 'DateTime{"booleanFalse": false, "booleanTrue": true}'
            ],
            'Integer values' => [
                'object'                 => new DateTime(),
                'values'                 => [
                    'integerNegative' => -123456789,
                    'integerPositive' => 123456789,
                ],
                'expectedToStringOutput' => 'DateTime{"integerNegative": -123456789, "integerPositive": 123456789}'
            ],
            'Double values' => [
                'object'                 => new DateTime(),
                'values'                 => [
                    'doubleNegative'                     => -123456789.99,
                    'doublePositive'                     => 123456789.99,
                    'doubleNegativeWithNegativeExponent' => -1.23456789e-20,
                    'doublePositiveWithNegativeExponent' => 1.23456789e-20,
                    'doubleNegativeWithPositiveExponent' => -1.23456789e-20,
                    'doublePositiveWithPositiveExponent' => 1.23456789e-20,
                ],
                'expectedToStringOutput' => 'DateTime{"doubleNegative": -123456789.99, "doublePositive": 123456789.99, "doubleNegativeWithNegativeExponent": -1.23456789E-20, "doublePositiveWithNegativeExponent": 1.23456789E-20, "doubleNegativeWithPositiveExponent": -1.23456789E-20, "doublePositiveWithPositiveExponent": 1.23456789E-20}'
            ],
            'String values' => [
                'object'                 => new DateTime(),
                'values'                 => [
                    'stringEmpty'            => '',
                    'stringHelloWorld'       => 'Hello World',
                    'stringWithNewline'      => 'Hello' . PHP_EOL . 'World',
                    'stringWithNumericValue' => '123',
                ],
                'expectedToStringOutput' => 'DateTime{"stringEmpty": "", "stringHelloWorld": "Hello World", "stringWithNewline": "Hello' . PHP_EOL . 'World", "stringWithNumericValue": "123"}'
            ],
            'Array values' => [
                'object'                 => new DateTime(),
                'values'                 => [
                    'arrayEmpty'                    => [],
                    'listArray'                     => ['John', 'Doe', 18],
                    'keyValueArray'                 => ['firstname' => 'John', 'lastname' => 'Doe', 'age' => 18],
                    'multidimensionalListArray'     => ['John', 'Doe', [10, 20, 30]],
                    'multidimensionalKeyValueArray' => ['name' => 'John Doe', 'age' => 18, 'father' => ['name' => 'John Doe Sr.']],
                ],
                'expectedToStringOutput' => 'DateTime{"arrayEmpty": [], "listArray": ["John", "Doe", 18], "keyValueArray": ["firstname": "John", "lastname": "Doe", "age": 18], "multidimensionalListArray": ["John", "Doe", [10, 20, 30]], "multidimensionalKeyValueArray": ["name": "John Doe", "age": 18, "father": ["name": "John Doe Sr."]]}'
            ],
            'Object values' => [
                'object'                 => new DateTime(),
                'values'                 => [
                    'objectImplementingStringable'    => new Test_ImplementStringable(),
                    'objectNotImplementingStringable' => new Test_NotImplementStringable(),
                ],
                'expectedToStringOutput' => 'DateTime{"objectImplementingStringable": Test_ImplementStringable{"name": "John Doe", "age": 18}, "objectNotImplementingStringable": MawebDK\ToStringBuilder\Test\Test_NotImplementStringable{?}}'
            ],
            'Object (enumeration) values' => [
                'object'                 => new DateTime(),
                'values'                 => [
                    'basicEnumeration'  => Test_BasicEnum::ONE,
                    'backedEnumeration' => Test_BackedEnum::ONE,
                ],
                'expectedToStringOutput' => 'DateTime{"basicEnumeration": MawebDK\ToStringBuilder\Test\Test_BasicEnum{?}, "backedEnumeration": MawebDK\ToStringBuilder\Test\Test_BackedEnum{?}}'
            ],
            'Object (function) values' => [
                'object'                 => new DateTime(),
                'values'                 => [
                    'anonymousFunction' => function(): string { return ''; },
                ],
                'expectedToStringOutput' => 'DateTime{"anonymousFunction": Closure{?}}'
            ],
            'resources' => [
                'object'                 => new DateTime(),
                'values'                 => [
                    'streamResource'    => fopen(filename: 'php://stdout', mode: 'w'),
                ],
                'expectedToStringOutput' => 'DateTime{"streamResource": resource(stream)}'
            ],
        ];
    }
}

class Test_SimpleClass {}
class Test_SimpleSubclass extends Test_SimpleClass {}

class Test_ImplementStringable implements Stringable
{
    public function __toString(): string
    {
        return 'Test_ImplementStringable{"name": "John Doe", "age": 18}';
    }
}

class Test_NotImplementStringable {}

enum Test_BasicEnum
{
    case ONE;
}

enum Test_BackedEnum: int
{
    case ONE = 1;
}