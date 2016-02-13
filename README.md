# Redbean traversing
Add traversing through associations for redbeans using decorators

## Requirements

- PHP >= 5.3.0

## Install
### Using Composer

```
composer require xire28/redbean-traversing
```

## Examples

### Seeds

- United States of America
	- Alabama
		- Lionel Richie (1949-06-20)
		- Felicia Day (1979-06-28)
	- Alaska
		- Anna Graceman (1999-08-01)
	- Arizona
		- Emma Stone (1988-11-06)
	- Arkansas
- China
	- Gensu
		- Wong Lee (1978-02-01)

### Retrieve all persons in the usa

```
<?php

class CountryTraversingDecorator extends \RedbeanTraversing\TraversingDecorator
{
    public function people(){
      return $this->hasManyThrough('ownPerson', ['ownState']);
    }
}

$usa = new CountryTraversingDecorator(R::load('country', 1));

echo '<ul>';
foreach($usa->people as $person){
	echo "<li>{$person->fullName}</li>";
}
echo '</ul>';
?>
```

#### Output
```
- Lionel Richie
- Felicia Day
- John Doe
- Logan Grove
- Anna Graceman
- Emma Stone
```

### Retrieve the country of a person

```
<?php
class PersonTraversingDecorator extends \RedbeanTraversing\TraversingDecorator
{
    public function country(){
      return $this->hasOneThrough(['state', 'country']);
    }
}

$lionelRichie = new PersonTraversingDecorator(R::load('person', 1));
echo $lionelRichie->country->name;
?>
```

#### Output
```
United States of America
```

### Use scoped associations
#### Define global decorator builders
##### Note
- Naming convention: "decorate" + capitablized bean type
- Functions are called when decorating nested associations

```
<?php
function decorateCountry($country){
	return new CountryTraversingDecorator($country);
}

function decorateState($state){
	return new StateTraversingDecorator($state);
}
?>
```

#### Define traversing decorators

```
<?php
class CountryTraversingDecorator extends \RedbeanTraversing\TraversingDecorator
{
    public function personOlderThan($age){
      return $this->hasManyThrough(['ownState', ['personOlderThan', $age]]);
    }
}

class StateTraversingDecorator extends \RedbeanTraversing\TraversingDecorator
{
    public function personOlderThan($age){
      return $this->traverseWithScope('ownPerson', function($person) use ($age){
      	return (new Datetime('now'))->format('Y') - (new DateTime($person->bornAt))->format('Y') > $age;
      });
    }
}
?>
```

#### Main
```
<?php
$usa = decorateCountry(R::load('country', 1));
echo '<ul>';
foreach($usa->personOlderThan(20) as $person){
	echo "<li>{$person->fullName}</li>";
}
echo '</ul>';
?>

#### Output
```
- Lionel Richie
- Felicia Day
- Emma Stone
```