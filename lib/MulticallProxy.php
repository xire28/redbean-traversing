<?php namespace RedbeanTraversing;

use IteratorAggregate;
use ArrayIterator;

class MulticallProxy implements IteratorAggregate {
    protected $collection;

    public function __construct($collection = []) {
        $this->collection = $collection;
    }

    /**
     * Try to map callback on all items
     *
     * @param Closure $callback
     *
     * @return MulticallProxy
     */
    protected function apply($callback) {
        $this->collection = array_compact(array_flatten(array_map($callback, $this->collection)));
        return $this;
    }

	/**
     * Try to call method on all items
     *
     * @return MulticallProxy
     */
	public function __call($method, $args) {
        return $this->apply(function($item) use ($method, $args){
            return call_user_func_array([$item, $method], $args);
        });
	}

	/**
     * Try to get property on all items
     *
     * @return mixed
     */
	public function __get($property) {
        return $this->apply(function($item) use ($property){
            return $item->$property;
        });
	}

    /**
     * Try to set property on all items
     *
     * @return mixed
     */
    public function __set($property, $value) {
        return $this->apply(function($item) use ($property, $value){
            return $item->$property = $value;
        });
    }

	/**
     * Returns an iterator to the collection.
     *
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->collection);
    }
}