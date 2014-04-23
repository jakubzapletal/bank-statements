# Bank Statements

[![Build Status](https://travis-ci.org/jakubzapletal/bank-statements.svg?branch=master)](https://travis-ci.org/jakubzapletal/bank-statements)
[![Coverage Status](https://coveralls.io/repos/jakubzapletal/bank-statements/badge.png?branch=master)](https://coveralls.io/r/jakubzapletal/bank-statements?branch=master)

PHP library to parse bank account statements. The purpose of this library is to standardize output from bank statement
and then easy to process in your application. Output of parsing includes `JakubZapletal\Component\BankStatement\Statement\StatementInterface` containing detail information
about statement and array of transactions by `JakubZapletal\Component\BankStatement\Statement\Transaction\TransactionInterface`.


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
