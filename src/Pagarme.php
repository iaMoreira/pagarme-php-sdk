<?php

namespace Pagarme;

use Exception;
use PagarMe\Client;
use stdClass;

class Pagarme {
  
  /**
     * @var \PagarMe\Client
    */
  private $pagarme;

  private $customer;

  private $card;

  private $transaction;

  private $transactionData;

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
    $this->pagarme = new Client($apiKey);
    $this->transactionData = [];
  }

  /**
   * @param array $payload
   * @return null|stdClass
   */
  public function createCustumer(array $payload) :?stdClass
  {
    try{
      $this->customer = $this->pagarme->customers()->create([
        'external_id' => $payload['id'],
        'name' => $payload['name'],
        'type' => $payload['type'] ?? 'individual',
        'country' => $payload['country'] ?? 'br',
        'email' => $payload['email'],
        'documents' => [
          [
            'type' => $payload['documents']['type'] ?? 'cpf',
            'number' => $payload['documents']['number'] ?? $this->clearField($payload['document'])
          ]
        ],
        'phone_numbers' => $payload['phone'] ? [$this->clearField($payload['phone'])] : $payload['phone_numbers'],
        'birthday' => $payload['birthday']
      ]);
      $this->setCustumer();
      return $this->customer;
    }catch(\Exception $ex) {
      $this->errors = $ex->getMessage();
      return null;
    }
  }
  
  /**
   * @param string $id = ID de cliente dentro do sistema da Pagar.me
   * @return null|stdClass
   */
  public function getCustomer(string $id) : ?stdClass
  {
    try {
      $this->customer = $this->pagarme->customers()->get([
        'id' => $id
      ]);
      $this->setCustumer();
      return $this->customer;

    } catch ( \PagarMe\Exceptions\PagarMeException $ex) {
      $this->errors = $ex->getMessage();  
      return null;
    }
  }
  
  /**
   * @param array|null $payload
   *
   * @return null|array
   */
  public function getCustomerList(array $payload = null) : ?array
  {
    try {
      return $this->pagarme->customers()->getList($payload);
    } catch (\Exception $ex) {
      $this->errors = $ex->getMessage();      
      return null;
    }
  }

  private function setCustumer() : void
  {
    // var_dump($this->customer->documents);
    $this->transactionData += [
      'customer' => [
        'external_id' => $this->customer->external_id,
        'name' => $this->customer->name,
        'type' => $this->customer->type,
        'country' => $this->customer->country,
        'documents' => [
          [
            'type'    => $this->customer->documents[0]->type,
            'number'  => $this->customer->documents[0]->number
          ]
        ],
        'phone_numbers' => $this->customer->phone_numbers,
        'email' => $this->customer->email
    ],
    ];
  }
  /**
   * @param string $param
   * @return string
   */
  private function clearField(string $param): string
  {
    return str_replace(['.', '/', '-', '(', ')', ',', ' '], '', $param);
  }

  /**
   * @param array $payload
   * @return \stdClass
   */
  public  function createCreditCard(array $payload)
  {
    $body = [
      'holder_name' => \strtoupper($payload['holder_name']),
      'number' => $payload['number'],
      'expiration_date' => $this->clearField($payload['expiration_date']),
      'cvv' => $payload['cvv'],
    ];

    if(isset($payload['customer_id'])){
      $body['customer_id'] = $payload['customer_id'];
    }
    try {
      $this->card = $this->pagarme->cards()->create($body);
      if($this->card->valid !== true){
        throw new Exception('Erro ao criar o cartão, verifique se os dados inseridos são válidos.');
      }
      $this->setCreditCard();
      return $this->card;
    } catch(\Exception $ex){
      $this->errors = $ex->getMessage();
      return null;
    }
  }

  /**
   * @param array $payload
   *
   * @return \stdClass
   */
  public function getCreditCard(string $cardId) : ?stdClass
  {
    try {
      $this->card = $this->pagarme->cards()->get([
        'id' => $cardId
      ]);
      
      $this->setCreditCard();
      return $this->card;
    } catch (\Exception $ex) {
      $this->errors = $ex->getMessage();
      return null;      
    }
  }

  /**
   * @param array|null $payload
   *
   * @return null|array
   */
  public function getCreditCardList(array $payload = null) : ?array
  {
    try {
      $cards = $this->pagarme->cards()->getList($payload);
      return $cards;
    } catch(Exception $ex){
      $this->errors = $ex->getMessage();
      return null;
    }
  }

  private function setCreditCard() 
  {
    $this->transactionData += [
      'payment_method'  => 'credit_card',
      'card_id'         => $this->card->id
    ] ;
  }

  public function billet(string $instruction, int $expirationDays = 3 )
  {
    $this->transactionData += [
      'payment_method'          => 'boleto',
      'boleto_expiration_date'  => date('Y-m-d', \strtotime('+' . $expirationDays . 'days')),
      'boleto_instructions'     => \substr($instruction, 0, 255)
    ];
  }

  public function payResquest(int $amount) : ?stdClass
  {
    $this->transactionData += [
      'amount' => $amount,
    ];

    try {
      $this->transaction = $this->pagarme->transactions()->create($this->transactionData);
      return $this->transaction;
    } catch(\Exception $ex) {
      $this->errors = $ex->getMessage();
      return null;
    }
  }
}