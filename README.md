# ToStringBuilder
Builder class for __toString() methods.

## Usage

Sample usage in a __toString() method:
```
public function __toString(): string
{
    $toStringBuilder = new ToStringBuilder(object: $this);
    
    return $toStringBuilder
        ->add('firstname', $this->firstname)
        ->add('lastname', $this->lastname)
        ->add('age', $this->age)
        ->build();
    }
}
```

Sample output:
```
Namespace\Classname{"firstname": "John", "lastname": "Doe", "age": 18}
```
