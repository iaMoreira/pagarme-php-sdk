<?php

namespace Pagarme\Services;

use Pagarme\Pagarme;

abstract class Service {

    /**
   * @var \Pagarme\Pagarme
  */
  protected $pagarme;
  protected $errors;
  public function __construct(Pagarme $pagarme)
  {
    $this->pagarme = $pagarme;  
  }

}