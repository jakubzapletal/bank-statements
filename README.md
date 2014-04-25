# Bank Statements

[![Build Status](https://travis-ci.org/jakubzapletal/bank-statements.svg?branch=master)](https://travis-ci.org/jakubzapletal/bank-statements)
[![Coverage Status](https://coveralls.io/repos/jakubzapletal/bank-statements/badge.png?branch=master)](https://coveralls.io/r/jakubzapletal/bank-statements?branch=master)

[![Latest Stable Version](https://poser.pugx.org/jakubzapletal/bank-statements/v/stable.png)](https://packagist.org/packages/jakubzapletal/bank-statements)
[![Total Downloads](https://poser.pugx.org/jakubzapletal/bank-statements/downloads.png)](https://packagist.org/packages/jakubzapletal/bank-statements)
[![Latest Unstable Version](https://poser.pugx.org/jakubzapletal/bank-statements/v/unstable.png)](https://packagist.org/packages/jakubzapletal/bank-statements)
[![License](https://poser.pugx.org/jakubzapletal/bank-statements/license.png)](https://packagist.org/packages/jakubzapletal/bank-statements)

This is a PHP library to parse bank account statements. The purpose of the library is to standardize outputs from bank statements
and then easy to process in your application. The result of the parsing is an instance of:
`JakubZapletal\Component\BankStatement\Statement\StatementInterface` containing detail informations
about a statement and an array of transactions instanced of `JakubZapletal\Component\BankStatement\Statement\Transaction\TransactionInterface`.


### Supported formats/bank list

* ABO (`*.gpc`) [[doc](doc/abo.md)]
 * Česká spořitelna (CZ): `JakubZapletal\Component\BankStatement\Parser\ABO\CeskaSporitelnaCZParser`
 * ČSOB (CZ): `JakubZapletal\Component\BankStatement\Parser\ABOParser`
 * Fio banka (CZ): `JakubZapletal\Component\BankStatement\Parser\ABOParser`
 * GE Money Bank (CZ): `JakubZapletal\Component\BankStatement\Parser\ABOParser`
 * Komerční banka (CZ), *alias KM format*: `JakubZapletal\Component\BankStatement\Parser\ABOParser`
 * Raiffeisenbank (CZ): `JakubZapletal\Component\BankStatement\Parser\ABOParser`
* XML
 * ČSOB (CZ) [[doc](doc/xml/csob_cz.md)]: `JakubZapletal\Component\BankStatement\Parser\XML\CSOBCZParser`
* CSV


## Installation

Note that Bank Statements is [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) compliant:

### Composer

If you don't have Composer [install](http://getcomposer.org/doc/00-intro.md#installation) it:

```bash
$ curl -s https://getcomposer.org/installer | php
```

Add `jakubzapletal/bank-statements` to `composer.json`:

```json
"require": {
    "jakubzapletal/bank-statements": "1.0.*@dev"
}
```

Refresh your dependencies:

```bash
$ php composer.phar update
```


## Usage

A parsing of each format is provided by a class implementing:

```php
JakubZapletal\Component\BankStatement\Parser\ParserInterface
```

Thanks to the interface we can rely on two main public methods: `parseFile` and `parseContent`.

* `parseFile` expects as an argument a **path to file** and then processes the parsing
* `parseContent` expects as an argument a **string of content** and then processes the parsing

Both methods return a class implementing:

```php
JakubZapletal\Component\BankStatement\Statement\StatementInterface
```

The statement class includes transaction items, which are classes implementing:

```php
JakubZapletal\Component\BankStatement\Statement\Transaction\TransactionInterface
```

This behaviour ensures the **same approach to the parsing and results for all cases**.

All abstract classes and standard classes are done to be easy to extend them and rewrite any part of the parsing process.

The basic statement class:

```php
JakubZapletal\Component\BankStatement\Statement\Statement
```

is set to be **countable** and **traverseable**, so we can call functions `count()` or `foreach()` on this class.
This functions manipulate with transactions inside the statement. If you need more functionality in the statement class,
I recommend extend this class.

### Examples

The parsing:

```php
use JakubZapletal\Component\BankStatement\Parser\ABOParser;

$parser = new ABOParser();

// by path to file
$path = '/path/to/file';
$statement = $parser->parseFile($path);

// by content
$content = 'string of data';
$statement = $parser->parseContent($content);
```

Manipulation with the statement:

```php
echo count($statement); // echo count of transaction items

foreach ($statement as $transaction) {
    // do something with each transaction
}

echo $statement->getAccountNumber(); // echo an account number of the statement
```


## Contributing

Contributions are welcome! Please see the [Contribution Guidelines](contributing.md).