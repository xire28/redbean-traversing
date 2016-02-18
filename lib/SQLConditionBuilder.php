<?php namespace RedbeanTraversing;

trait SQLConditionBuilder {
	protected	$withSql = '',
				$withParams = [];

	/** 
	* Progressively construct the sql request by adding clauses
	* 
	* @param string $statement
	* @param array|string $params
	*
	* @return OODBBean
	*/
	public function withStatement($statement, $params = []){
		$this->withSql .= "{$statement} ";
		$this->withParams = array_merge($this->withParams, array_wrap($params));
		$this->bean->with($this->withSql, $this->withParams);
		return $this;
	}

	/** 
	* Append "AND" clause to the sql statement
	*
	* @param Closure|function $callback optional
	*
	* @return OODBBean
	*/
	public function _and($callback = null){
		return isset($callback)? $this->withStatement('AND')->group($callback) : $this->withStatement('AND');
	}

	/** 
	* Append "OR" clause to the sql statement
	* 
	* @param Closure|function $callback optional
	*
	* @return OODBBean
	*/
	public function _or($callback = null){
		return isset($callback)? $this->withStatement('OR')->group($callback) : $this->withStatement('OR');
	}

	public function group($callback){
		$this->withStatement('(');
		$callback($this);
		$this->withStatement(')');
		return $this;
	}

	/** 
	* Append condition to the sql statement
	* 
	* @param string $statement
	* @param array|string $params
	*
	* @return OODBBean
	*/
	public function where($statement, $params = []) {
		if($this->$withSql === '') $this->_and();
		return  $this->withStatement($statement, $params) : ;
	}
}