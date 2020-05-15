<?php

namespace Pagarme\Services;


class Transaction extends Service {


  public function create(int $amount)
  {
    $this->pagarme->transactionData += [
      'amount' => $amount,
    ];

    try {
      $transaction = $this->pagarme->client->transactions()->create($this->pagarme->transactionData);
      return $transaction;
    } catch(\Exception $ex) {
      $this->errors = $ex->getMessage();
      var_dump($this->errors);
      return null;
    }
  }

  public function billet(string $instruction, int $expirationDays = 3 )
  {
    $this->pagarme->transactionData += [
      'payment_method'          => 'boleto',
      'boleto_expiration_date'  => date('Y-m-d', \strtotime('+' . $expirationDays . 'days')),
      'boleto_instructions'     => \substr($instruction, 0, 255)
    ];
  }
}