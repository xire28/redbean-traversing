<?php namespace RedbeanTraversing;

/** 
* flatten an array (http://snippets.dzone.com/posts/show/4660)
* @param array $array array to flatten
* 
* @return array
*/
function array_flatten($array) {
	$i = 0;

	while ($i < count($array))
	{
		if (is_array($array[$i]))
			array_splice($array,$i,1,$array[$i]);
		else
			++$i;
	}
	return $array;
}

/** 
* Remove null from array
* @param array $array array to compact
* 
* @return array
*/ 
function array_compact($array) {
	return array_filter($array, function($item){
		return isset($item);
	});
}

/** 
* Clone an array
* @param array $array array to clone
* 
* @return array
*/ 
function array_clone($array) {
	return $array;
}

/** 
* Wrap object in an array
* @param mixed $object object to wrap
* 
* @return array
*/ 
function array_wrap($object) {
	return is_array($object)? $object : [$object];
}

