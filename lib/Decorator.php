<?php namespace RedbeanTraversing;

class Decorator {
  /**
   * @var
   */
  public $object;

  /**
   * Class constructor.
   *
   * @param object $object
   *   The object to decorate.
   */
  public function __construct($object) {
    $this->object = $object;
  }

  /**
   * @param $method
   * @param $args
   *
   * @return mixed
   *
   * @throws \Exception
   */
  public function __call($method, $args) {
     return call_user_func_array([$this->object, $method], $args);
  }

  /**
   * @param $property
   *
   * @return mixed
   */
  public function __get($property) {
      return $this->object->$property;
  }


  /**
   * @param $property
   *
   * @return null
   */
  public function __set($property, $value) {
      $this->object->$property = $value;
  }
}