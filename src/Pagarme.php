<?php

namespace Pagarme;

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
}