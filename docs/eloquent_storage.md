# Eloquent Storage

Here we show how to store data in database using [Eloquent ORM](http://laravel.com/docs/4.2/eloquent).

## Usage

Create an eloquent model:

```php
<?php
class Payment extends Illuminate\Database\Eloquent\Model
{
    protected $table = 'payments';
}
```

Register a storage for it 

```php
// app/config/packages/payum/payum-laravel-package/config.php

use Payum\LaravelPackage\Storage\EloquentStorage;

return array(
    'storages' => array(
        'Payment' => new EloquentStorage('Payment'),
    )
);
```

## Models 

The package provides two models `Payum\LaravelPackage\Model\Token` and `Payum\LaravelPackage\Model\Payment` which may be reused directly or extend with some custom logic.
Here's the models schemas:

Order:
```php
<?php

\Schema::create('payum_payments', function($table) {
    /** @var \Illuminate\Database\Schema\Blueprint $table */
    $table->bigIncrements('id');
    $table->text('details');
    $table->string('number');
    $table->string('description');
    $table->string('clientId');
    $table->string('clientEmail');
    $table->string('totalAmount');
    $table->string('currencyCode');
    $table->timestamps();
});
```


Token:

```php
<?php

\Schema::create('payum_tokens', function($table) {
    /** @var \Illuminate\Database\Schema\Blueprint $table */
    $table->string('hash')->primary();
    $table->text('details');
    $table->string('targetUrl');
    $table->string('afterUrl');
    $table->string('gatewayName');
    $table->timestamps();
});
```

Back to [index](index.md).
