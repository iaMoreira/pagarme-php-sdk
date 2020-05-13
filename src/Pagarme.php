<?php

namespace Pagarme;

use ArrayObject;
use PagarMe\Client;

class Pagarme {
  
  /**
     * @var \PagarMe\Client
    */
  private $pagarme;

   /**
     * @param string $apiKey
    */
  public function __construct(string $apiKey)
  {
    $this->pagarme = new Client($apiKey);
  }

  /**
   * @param array $payload
   * @return mixed|void
   */
  public function createCustumer(array $payload) :\ArrayObject
  {
    $customer = $this->pagarme->customers()->create([
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

    return $customer;
  }
  
  /**
   * @param string $id = ID de cliente dentro do sistema da Pagar.me
   * @return \ArrayObject
   */
  public function getCustomer(string $id) 
  {
    $customer = $this->pagarme->customers()->get([
      'id' => $id
    ]);

    return $customer;
  }
  
  
  public function getCustomerList()
  {
    return $this->pagarme->customers()->getList();
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
    
    $card = $this->pagarme->cards()->create($body);

    if($card->valid !== true){
      return null; // TODO cria uma exeção 
    }

    return $card;
  }

  /**
   * @param array $payload
   *
   * @return \ArrayObject
   */
  public function getCreditCard(string $cardId)
  {
    $card = $this->pagarme->cards()->get([
      'id' => $cardId
    ]);

    return $card;
  }

  /**
   * @param array|null $payload
   *
   * @return \ArrayObject
   */
  public function getCreditCardList( array $payload = null)
  {
  
    $cards = $this->pagarme->cards()->getList($payload);
    return $cards;
  }
}