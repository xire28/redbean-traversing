<?php namespace RedbeanTraversing;

/** 
* Flatten an array
* @param array $array array to flatten
* 
* @return array
*/ 
function array_flatten($array) {
	$flattenArray = [];
	array_walk_recursive($array, function($a) use (&$flattenArray) { 
		$flattenArray[] = $a; 
	});
	return $flattenArray;
}

/** 
* Wrap object in an array
* @param array $array object to wrap
* 
* @return array
*/ 
function array_wrap($object){
	return is_array($object)? $object : [$object];
}



