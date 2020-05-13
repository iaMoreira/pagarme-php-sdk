# Pagar.me PHP SDK

Essa SDK foi construída com o intuito de torná-la flexível, de forma que todos possam utilizar todas as features, de todas as versões de API.

Você pode acessar a documentação oficial do Pagar.me acessando esse link.

### Instalação
Instale a biblioteca utilizando o comando

`composer require iamoreira/pagarme-php-sdk`

### Configuração

Para incluir a biblioteca em seu projeto, basta fazer o seguinte:

```php
<?php
require('vendor/autoload.php');

$pagarme = new Pagarme\Pagarme('SUA_CHAVE_DE_API');
```

### Clientes
Clientes representam os usuários de sua loja, ou negócio. Este objeto contém informações sobre eles, como nome, e-mail e telefone, além de outros campos.

#### Criando um cliente
```php
<?php
$pagarme->createCustumer([
    'id'        => '#123456789',
    'name'      => 'João das Neves',
    'email'     => 'joaoneves@norte.com',
    'document'  => '198.789.700-51',
    'phone'     => '+55 (73) 98150-9999',
    'birthday'  => '1990-04-20'
  ]);  
```
Além dos paramêtros bases, é possível injetar uma forma mais completa do cliente:

```php
<?php
$pagarme->createCustumer([
    'id'        => '#123456789',
    'name'      => 'João das Neves',
    'type'      => 'individual',
    'country'   => 'br',
    'email'     => 'joaoneves@norte.com',
    'documents' => [
      [
        'type'    => 'cpf',
        'number'  => '11111111111'
      ]
    ],
    'phone_numbers' => [
      '+5511999999999',
      '+5511888888888'
    ],
    'birthday'  => '1985-01-01'
  ]);  
``` 
#### Retornando clientes
```php
<?php
$customers = $pagarme->getCustomerList();
``` 

#### Retornando um cliente
```php
<?php
$customer = $pagarme->getCustomer([
    'id' => 'ID_DO_CLIENTE'
]);
``` 
