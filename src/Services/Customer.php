<?php

namespace Pagarme\Services;

class Customer extends Service {

  /**
   * @param array $payload
   * @return null|stdClass
   */
  public function create(array $payload) :?stdClass
  {
    try{
      $customer = $this->pagarme->client->customers()->create([
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
    }catch(\Exception $ex) {
      $this->errors = $ex->getMessage();
      return null;
    }
  }
  
  /**
   * @param string $id = ID de cliente dentro do sistema da Pagar.me
   * @return null|stdClass
   */
  public function get(string $id)
  {
    try {
      $customer = $this->pagarme->client->customers()->get([
        'id' => $id
      ]);
      return $customer;

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
  public function getList(array $payload = null) : ?array
  {
    try {
      return $this->pagarme->client->customers()->getList($payload);
    } catch (\Exception $ex) {
      $this->errors = $ex->getMessage();      
      return null;
    }
  }

  /**
   * @param string|array $payload = ID de cliente ou dados para cadastro
   * @return null|stdClass
   */
  public function set($payload) 
  {
    if(is_array($payload)){
      $customer = $this->create($payload);
    }else {
      $customer = $this->get($payload);
    }

    $this->pagarme->transactionData += [
        'customer' => [
          'external_id' => $customer->external_id,
          'name' => $customer->name,
          'type' => $customer->type,
          'country' => $customer->country,
          'documents' => [
            [
              'type'    => $customer->documents[0]->type,
              'number'  => $customer->documents[0]->number
            ]
          ],
          'phone_numbers' => $customer->phone_numbers,
          'email' => $customer->email
      ],
    ];

    return $customer;
  }
}