<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Citoyen extends BaseCitoyen
{
  public function isPasswordCorrect($password) {
    return ($this->password == sha1($password));
  }
  public function setPassword($password) {
    return $this->_set('password', sha1($password));
  }
}
