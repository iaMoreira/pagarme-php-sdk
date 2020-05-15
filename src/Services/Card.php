<?php

namespace Pagarme\Services;

class Card extends Service {


  /**
   * @param array $payload
   * @return \stdClass
   */
  public  function create(array $payload)
  {
    $body = [
      'holder_name' => \strtoupper($payload['holder_name']),
      'number' => $payload['number'],
      'expiration_date' => $this->pagarme->clearField($payload['expiration_date']),
      'cvv' => $payload['cvv'],
    ];

    if(isset($payload['customer_id'])){
      $body['customer_id'] = $payload['customer_id'];
    }

    $card = $this->pagarme->client->cards()->create($body);
  
    if($card->valid !== true){
      throw new Exception('Erro ao criar o cartão, verifique se os dados inseridos são válidos.');
    }

    return $card;
  }

  /**
   * @param array $payload
   *
   * @return \stdClass
   */
  public function get(string $cardId)
  {
    $card = $this->pagarme->client->cards()->get([
      'id' => $cardId
    ]);
    
    return $card;
  }

  /**
   * @param array|null $payload
   *
   * @return null|array
   */
  public function getList(array $payload = null)
  {
    $cards = $this->pagarme->client->cards()->getList($payload);
    return $cards;
  }

  /**
   * @param string|array $payload
   * @return \stdClass
   */
  public function set($payload) 
  {
    if(is_array($payload)){
      $card = $this->create($payload);
    }else {
      $card = $this->get($payload);
    }

    $this->pagarme->transactionData += [
      'payment_method'  => 'credit_card',
      'card_id'         => $card->id
    ];

    return $card;
  }
}