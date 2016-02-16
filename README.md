# Redbean traversing
Add traversing through associations with scopes for redbeans

## Requirements

- PHP >= 5.4.0

## Install
### Using Composer

```
composer require xire28/redbean-traversing
```

## Note
This package is composed of two parts:
- `MulticallProxy` to use a collection like an object and apply the operation on all the items simultaneously
- `SQLConditionBuilder` to progressively construct the SQL query filter

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
    return $this->multi()->ownState->ownPerson;
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
- Anna Graceman
- Emma Stone
```

### Retrieve all adult persons in the usa
```
<?php

class BaseModel extends RedBean_SimpleModel
{
  	use RedbeanTraversing\ModelTraversing;
}

class Model_Country extends BaseModel
{
	public function adultPersons(){
    	return $this->multi()->ownState->isAdult()->ownPerson;
	}
}

class Model_State extends BaseModel
{
  	use PersonScope;
}

trait PersonScope {
	public function isAdult(){
		return $this->personOlderThan(17);
	}
	public function personOlderThan($age){
    	return $this->where('TIMESTAMPDIFF(YEAR, born_at, CURDATE()) > ?', $age);
	}
}

$usa = R::load('country', 1);
echo '<ul>';
foreach($usa->adultPersons() as $person){
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

### Build complex queries
```
<?php

class BaseModel extends RedBean_SimpleModel
{
  	use RedbeanTraversing\ModelTraversing;
}

class Model_Country extends BaseModel {}
class Model_State extends BaseModel {}

$usa = R::load('country', 1);
echo '<ul>';
foreach($usa->multi()->group(function($q){ return $q->where('name LIKE "Ar%"')->_or()->where('name = ?', 'Alabama'); })->ownState->ownPerson as $person){
	echo "<li>{$person->fullName}</li>";
}
echo '</ul>';
?>
```
#### SQL statements generated
```
SELECT `country`.* FROM `country` WHERE (`id` IN ( 1 ))
SELECT `state`.* FROM `state` WHERE country_id = '1' AND ( name LIKE "Ar%" OR name = 'Alabama' )
SELECT `person`.* FROM `person` WHERE state_id = '1'
SELECT `person`.* FROM `person` WHERE state_id = '3'
```

#### Output
```
- Lionel Richie
- Felicia Day
- Emma Stone
```
