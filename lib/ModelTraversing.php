<?php namespace RedbeanTraversing;

trait ModelTraversing {
	use SQLConditionBuilder;

	/** 
	* Get an association proxy for the bean
	* 
	* @return Multicall
	*/
	public function multi(){
		return static::buildMulticallProxy([$this]);
	}

	public static function buildMulticallProxy($beans){
		return new MulticallProxy($beans);
	}
}