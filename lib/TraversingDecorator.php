<?php namespace RedbeanTraversing;

class TraversingDecorator extends Decorator {

	/** 
	* Traverse association with scope
	* @param string $association association to traverse
	* @param string | function $scope
	*
	* @return array[OODBBean]
	*/
	public function traverseWithScope($association, $scope){
		return array_filter($this->{$association}?: [], $scope);
	}

	/** 
	* Retrieve a bean through nested associations
	* @param array[string | array[function [, array]]] $associations Associations to traverse from left to right 
	*
	* @return OODBBean | NULL 
	*/ 
	public function hasOneThrough($associations) {
		$associated = static::traverseMany([$this->object], $associations);
		return array_shift($associated);
	}

	/** 
	* Retrieve many beans through nested associations
	* @param array[string | array[function [, array]]] $associations Associations to traverse from left to right 
	*
	* @return array[OODBBean]
	*/
	public function hasManyThrough($associations) {
		return static::traverseMany([$this->object], $associations);
	}

	/** 
	* Retrieve beans through nested associations on multiple beans of the same type (recursive) and decorate them if a decoration function if defined
	* @param array[OODBBean] $beans
	* @param array[string | array[function [, array]]] $associations Associations to traverse from left to right 
	*
	* @return OODBBean | NULL 
	*/
	protected static function traverseMany($beans, $associations) {
   		$associated = array_map('static::tryDecorateBean', static::flatMapAssociation($beans, array_shift($associations)));
	    return count($associations) > 0 ? static::traverseMany($associated, $associations) : $associated;
    }

    /** 
	* Retrieve beans through the association on multiple beans
	* @param OODBBean $bean
	* @param string | array[function [, array]] $association Association to traverse
	* 
	* @return array[OODBBean]
	*/
    protected static function flatMapAssociation($beans, $association) {
    	return array_filter(array_flatten(array_map(function($bean) use ($association) {
    		$associated = is_array($association)? call_user_func_array([$bean, $association[0]], array_wrap($association[1])) : $bean->$association;
    		return array_wrap($associated);
		}, $beans)));
    }

    /** 
	* Try to decorate a bean using his decoration function (allows to define and use custom associations on intermediary beans)
	* @param OODBBean $bean
	* 
	* @return Decorator
	*/
    protected static function tryDecorateBean($bean) {
    	$capitalizedType = ucfirst($bean->getMeta('type'));
    	$decorator = "decorate{$capitalizedType}";
    	return is_callable($decorator)? $decorator($bean) : $bean;
    }

	/**
	* @param $property
	*
	* @return mixed
	*/
	public function __get($property) {
		return method_exists($this, $property)? $this->$property() : parent::__get($property);
	}
}