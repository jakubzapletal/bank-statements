# Bank Statements

[![Build Status](https://travis-ci.org/jakubzapletal/bank-statements.svg?branch=master)](https://travis-ci.org/jakubzapletal/bank-statements)
[![Coverage Status](https://coveralls.io/repos/jakubzapletal/bank-statements/badge.png?branch=master)](https://coveralls.io/r/jakubzapletal/bank-statements?branch=master)

[![Latest Stable Version](https://poser.pugx.org/jakubzapletal/bank-statements/v/stable.png)](https://packagist.org/packages/jakubzapletal/bank-statements)
[![Total Downloads](https://poser.pugx.org/jakubzapletal/bank-statements/downloads.png)](https://packagist.org/packages/jakubzapletal/bank-statements)
[![Latest Unstable Version](https://poser.pugx.org/jakubzapletal/bank-statements/v/unstable.png)](https://packagist.org/packages/jakubzapletal/bank-statements)
[![License](https://poser.pugx.org/jakubzapletal/bank-statements/license.png)](https://packagist.org/packages/jakubzapletal/bank-statements)

This is a PHP library to parse bank account statements. The purpose of the library is to standardize outputs from bank statements
and then easy to process in your application. The output of parsing includes
`JakubZapletal\Component\BankStatement\Statement\StatementInterface` containing detail informations
about a statement and an array of transactions by `JakubZapletal\Component\BankStatement\Statement\Transaction\TransactionInterface`.


### Supported formats/bank list

* [ABO](doc/abo.md) (`*.gpc`)
 * Česká spořitelna (CZ): `JakubZapletal\Component\BankStatement\Parser\ABO\CeskaSporitelnaCZParser`
 * ČSOB (CZ): `JakubZapletal\Component\BankStatement\Parser\ABOParser`
 * Fio banka (CZ): `JakubZapletal\Component\BankStatement\Parser\ABOParser`
 * GE Money Bank (CZ): `JakubZapletal\Component\BankStatement\Parser\ABOParser`
 * Komerční banka (CZ), *alias KM format*: `JakubZapletal\Component\BankStatement\Parser\ABOParser`
 * Raiffeisenbank (CZ): `JakubZapletal\Component\BankStatement\Parser\ABOParser`
* XML
 * ČSOB (CZ):
* CSV
 * Komerční banka (CZ):


### Contributing

Please see the [Contribution Guidelines](contributing.md).
