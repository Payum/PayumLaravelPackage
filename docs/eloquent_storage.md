# Eloquent Storage

Here we show how to store data in database using [Eloquent ORM](http://laravel.com/docs/4.2/eloquent).

## Usage

Create an eloquent model:

```php
<?php
class Order
{
    protected $table = 'orders';
}
```

Register a storage for it 

```php
// app/config/packages/payum/payum-laravel-package/config.php

use Payum\LaravelPackage\Storage\EloquentStorage;

return array(
    'storages' => array(
        'Order' => new EloquentStorage('Order'),
    )
);
```

## Models 

The package provides two models `Payum\LaravelPackage\Model\Token` and `Payum\LaravelPackage\Model\Order` which may be reused directly or extend with some custom logic.
Here's the models schemas:

Order:
```php
<?php

\Schema::create('payum_orders', function($table) {
    /** @var \Illuminate\Database\Schema\Blueprint $table */
    $table->bigIncrements('id');
    $table->text('details');
    $table->string('number');
    $table->string('description');
    $table->string('clientId');
    $table->string('clientEmail');
    $table->string('totalAmount');
    $table->string('currencyCode');
    $table->string('currencyDigitsAfterDecimalPoint');
    $table->timestamps();
});
```


Token:

```php
<?php

\Schema::create('payum_tokens', function($table) {
    /** @var \Illuminate\Database\Schema\Blueprint $table */
    $table->string('hash', 36)->primary();
    $table->text('details');
    $table->string('targetUrl');
    $table->string('afterUrl');
    $table->string('paymentName');
    $table->timestamps();
});
```

Back to [index](index.md).
