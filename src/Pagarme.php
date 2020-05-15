<?php

namespace Pagarme;

use Exception;
use PagarMe\Client;
use Pagarme\Services\Card;
use Pagarme\Services\Customer;
use Pagarme\Services\Transaction;
use stdClass;

class Pagarme {
  
  /**
     * @var \PagarMe\Client
  */
  public $client;

  /**
   * @var Pagarme\Services\Customer
  */
  private $customer;
  
  /**
   * @var Pagarme\Services\Card
  */
  private $card;
  
  /**
   * @var Pagarme\Services\Transaction
  */
  private $transaction;

  public $transactionData;

  private $errors;

  public function getError() : ?string
  {
    return $this->errors;
  }

   /**
     * @param string $apiKey
    */
  public function __construct(string $apiKey)
  {
    $this->client = new Client($apiKey);
    $this->transactionData = [];
    
    $this->customer = new Customer($this);
    $this->card = new Card($this);
    $this->transaction = new Transaction($this);
  }

  /**
   * @return Pagarme\Services\Costumer
  */
  public function customers()
  {
    return $this->customer;
  }
  
  /**
   * @return Pagarme\Services\Card
  */
  public function cards()
  {
    return $this->card;
  }

  /**
   * @return Pagarme\Services\Transaction
  */
  public function transactions()
  {
    return $this->transaction;
  }

  /**
   * @param string $param
   * @return string
   */
  public function clearField(string $param): string
  {
    return str_replace(['.', '/', '-', '(', ')', ',', ' '], '', $param);
  }

}