# Bank Statements

[![Build Status](https://travis-ci.org/jakubzapletal/bank-statements.svg?branch=master)](https://travis-ci.org/jakubzapletal/bank-statements)
[![Coverage Status](https://coveralls.io/repos/jakubzapletal/bank-statements/badge.png?branch=master)](https://coveralls.io/r/jakubzapletal/bank-statements?branch=master)

PHP library to parse bank account statements

### Supported formats

* [ABO](doc/abo.md) (`*.gpc`)
 * Česká spořitelna (CZ) `JakubZapletal\Component\BankStatement\Parser\ABO\CeskaSporitelnaCZParser`
 * Fio banka (CZ) `JakubZapletal\Component\BankStatement\Parser\ABOParser`
 * Komerční banka, alias KM format (CZ) `JakubZapletal\Component\BankStatement\Parser\ABOParser`
 * Raiffeisenbank (CZ) `JakubZapletal\Component\BankStatement\Parser\ABOParser`
* XML
 * ČSOB (CZ)
 * GE Money Bank (CZ)
