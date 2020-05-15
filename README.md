# Pagar.me PHP SDK

Essa SDK foi construída com o intuito de torná-la flexível, de forma que todos possam utilizar todas as features, de todas as versões de API.

Você pode acessar a documentação oficial do Pagar.me acessando esse [link](https://docs.pagar.me/).

## Índice
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Transações](#transações)
  - [Criando uma transação](#criando-uma-transação)
    - [Cartão de Crédito](#cartão-de-credito)
    - [Boleto](#boleto)
  - [Capturando uma transação](#capturando-uma-transação)
  - [Estornando uma transação](#estornando-uma-transação)
- [Clientes](#clientes)
  - [Criando um cliente](#criando-um-cliente)
  - [Retornando clientes](#retornando-clientes)
  - [Retornando um cliente](#retornando-um-clientes)
- [Cartões](#cartões)
  - [Criando cartões](#criando-cartões)
  - [Retornando cartões](#retornando-cartões)
  - [Retornando um cartão](#retornando-um-cartão)

## Instalação
Instale a biblioteca utilizando o comando

`composer require iamoreira/pagarme-php-sdk`

## Configuração

Para incluir a biblioteca em seu projeto, basta fazer o seguinte:

```php
<?php
require('vendor/autoload.php');

$pagarme = new Pagarme\Pagarme('SUA_CHAVE_DE_API');
```




## Transações

Nesta seção será explicado como utilizar transações no Pagar.me com essa biblioteca.

### Criando uma transação
O exemplo abaixo mostra a forma mais simples de executar uma transação, para isso é abstraído outras informações que serão melhor explicadas nas próximas secções. Mas primeiro é necessário entender que para executar o pagamento deve-se setar o cliente e o cartão/boleto primeiro.

#### Cartão de Crédito
```php
<?php
# é necessário cadastrar um novo cliente ou setar um cliente cadastrado.
$pagarme->customers()->set([
    'id'        => '#123456789',
    'name'      => 'João das Neves',
    'email'     => 'joaoneves@norte.com',
    'document'  => '198.789.700-51',
    'phone'     => '+55 (73) 98150-9999',
    'birthday'  => '1990-04-20'
  ]);

$pagarme->cards()->set([
    'holder_name'     => 'Yoda',
    'number'          => '4242424242424242',
    'expiration_date' => '1225',
    'cvv'             => '123'
]);

$transaction = $pagarme->transactions()->create(1000);
```

#### Boleto
Digamos que já temos cliente cadastro cujo o ID dele é `123456789`, então vamos seta-lo para essa transação.
```php
<?php
$pagarme->costumer()->set('ID_DO_CLIENTE'); # setando um cliente cadastrado

$billet = $pagarme->billet('INSTRUÇÕES');

$transaction = $pagarme->transactions()->create(1000);
```

### Capturando uma transação
```php
<?php
$capturedTransaction = $pagarme->transactions()->capture([
    'id' => 'ID_OU_TOKEN_DA_TRANSAÇÃO',
    'amount' => VALOR_TOTAL_COM_CENTAVOS
]);
```

### Estornando uma transação
```php
<?php
$refundedTransaction = $pagarme->transactions()->refund([
    'id' => 'ID_OU_TOKEN_DA_TRANSAÇÃO',
]);
```


## Clientes
Clientes representam os usuários de sua loja, ou negócio. Este objeto contém informações sobre eles, como nome, e-mail e telefone, além de outros campos.

### Criando um cliente
```php
<?php
# apenas cria e retorna um cliente
$customer = $pagarme->customers()->create([
    'id'        => '#123456789',
    'name'      => 'João das Neves',
    'email'     => 'joaoneves@norte.com',
    'document'  => '198.789.700-51',
    'phone'     => '+55 (73) 98150-9999',
    'birthday'  => '1990-04-20'
  ]);  

# cria um cliente e ainda seleciona para transação
$customer = $pagarme->customers()->set([
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
$pagarme->costumer()->create([
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
### Retornando clientes
```php
<?php
$customers = $pagarme->customers()->getList();
``` 
Ou também se necessário, você pode filtrar por algum dado específico do cliente, como mostra o exemplo com `name` abaixo:
```php
<?php
$customers = $pagarme->customers()->getList([
  'name'  => 'Yoda'
]);
``` 

### Retornando um cliente
```php
<?php
# apenas retorna o cliente cadastrado
$customer = $pagarme->costumer()->get('ID_DO_CLIENTE');

# retorna o cliente e seleciona ele para uma transação
$customer = $pagarme->costumer()->set('ID_DO_CLIENTE');

``` 

## Cartões
Sempre que você faz uma requisição através da nossa API, nós guardamos as informações do portador do cartão, para que, futuramente, você possa utilizá-las em novas cobranças, ou até mesmo implementar features como one-click-buy.

### Criando cartões
```php
<?php
$card = $pagarme->cards()->create([
    'holder_name'     => 'Yoda',
    'number'          => '4242424242424242',
    'expiration_date' => '1225',
    'cvv'             => '123'
]);
```

Também há a opção de atribuir o cartão ao um cliente passando o um atríbuto a mais `customer_id`, isso garante que só esse cliente tem permissão para usar esse cartão.
```php
<?php
$card = $pagarme->cards()->create([
    ...
    'costumer_id' => 'ID_DO_CLIENTE'
]);
```

#### Cobrança de R$1,23
 
Essa cobrança é realizada para validar o cartão que pode vir a ser utilizado na criação de uma transação (objeto transaction) ou de uma assinatura (subscription). Para isso, a API Pagar.me envia uma requisição ao banco emissor pedindo autorização de reserva no valor de **R$1,23** e, caso seja feita com sucesso, cria um novo cartão em nossa base marcando como válido.

É importante notar que o valor de R$1,23 `é estornado` para o portador do cartão no exato momento após a validação dos dados.

### Retornando cartões

```php
<?php
$cards = $pagarme->cards()->getList();
```

Se necessário, você pode filtrar por algum dado específico do cartão, por exemplo, o código abaixo irá trazer todos os cartões da bandeira `visa`:
```php
<?php
$visaCards = $pagarme->cards()->getList([
    'brand' => 'visa'
]);
```
#### Filtros

Todos os filtros são os mesmos atributos de retorno do cartão criado, eles  também podem ser usados para buscas em ranges usando os prefixos:

|Prefixo	    | Significado|
:--------:    | :----------:
|<	          | menor que|
|>	          | maior que|
|<=	          | menor ou igual a|
|>=    	      | maior ou igual a|

Por exemplo, para buscar em um range de date_created e outros:
```php
<?php
$visaCards = $pagarme->cards()->getList([
    'created_at'  => '>=1483236000000',
    'created_at'  => '<=1484689847590',
    'customer_id' => '123456789',
    'holder_name' => 'Yoda'
    ...
]);
```

Para campos que sejam strings, a comparação é lexicográfica, letras maiúsculas sendo 'maiores' que minúsculas.
### Retornando um cartão
```php
<?php
$card = $pagarme->cards()->get('ID_DO_CARTÃO');

# retorna o cartão e seleciona ele para transação.
$card = $pagarme->cards()->set('ID_DO_CARTÃO');

```
