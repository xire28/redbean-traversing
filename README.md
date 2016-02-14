# Redbean traversing
Add traversing through associations for redbeans

## Requirements

- PHP >= 5.4.0

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

class Model_Country extends RedBean_SimpleModel
{
  use RedbeanTraversing\ModelTraversing;
  public function people(){
    return $this->manyThrough(['ownState', 'ownPerson']);
  }
}

$usa = R::load('country', 1);

echo '<ul>';
foreach($usa->people() as $person){
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

class Model_Person extends RedBean_SimpleModel
{
  use RedbeanTraversing\ModelTraversing;
  public function country(){
    return $this->oneThrough(['state', 'country']);
  }
}

$lionelRichie = R::load('person', 1);
echo $lionelRichie->country()->name;

?>
```

#### Output
```
United States of America
```

### Retrieve all adult persons in the usa using named scopes
```
<?php

class BaseModel extends RedBean_SimpleModel
{
  use RedbeanTraversing\ModelTraversing;
}

class Model_Country extends BaseModel
{
  public function personOlderThan($age){
    return $this->manyThrough(['ownState', ['personOlderThan', $age]]);
  }
}

class Model_State extends BaseModel
{
  public function personOlderThan($age){
    return $this->traverseWithScope('ownPerson', Model_Person::olderThanScope($age));
  }
}

class Model_Person extends BaseModel
{
  public function isOlderThan($age){
    return (new Datetime('now'))->format('Y') - (new DateTime($this->bornAt))->format('Y') > $age;
  }

  public static function olderThanScope($age){
    return function($person) use ($age){
      return $person->isOlderThan($age);
    };
  }
}

$usa = R::load('country', 1);
echo '<ul>';
foreach($usa->personOlderThan(17) as $person){
	echo "<li>{$person->fullName}</li>";
}
echo '</ul>';

?>
```

#### Output
```
- Lionel Richie
- Felicia Day
- Emma Stone
```

### Retrieve all persons starting with `a` in the usa using unnamed scopes

```
<?php

class Model_Country extends RedBean_SimpleModel
{
	use RedbeanTraversing\ModelTraversing;
}

$usa = R::load('country', 1);

echo '<ul>';
foreach($usa->manyThrough(['ownState', ['traverseWithScope', ['ownPerson', function($person){
  return strpos($person->fullName, 'A') === 0;
}]]]) as $person){
	echo "<li>{$person->fullName}</li>";
}

R::close();

?>
```

#### Output
```
- Anna Graceman
```

