<?php

namespace JakubZapletal\Component\BankStatement\Parser\ABO;

use JakubZapletal\Component\BankStatement\Parser\ABOParser;
use JakubZapletal\Component\BankStatement\Statement\BankAccount;

class TatraBankaSKParser extends ABOParser
{
    /**
     * @param $line
     * @return BankAccount
     */
    protected function parseAccountNumber($line)
    {
        $prefix = ltrim(substr($line, 13, 6), '0');
        $number = ltrim($this->decodeAccountNumber(substr($line, 3, 10)), '0');
        return new BankAccount($prefix, $number, '1100');
    }

    /**
     * @param $line
     * @return BankAccount
     */
    protected function parseCounterAccountNumber($line)
    {
        $prefix = ltrim(substr($line, 29, 6), '0');
        $number = ltrim($this->decodeAccountNumber(substr($line, 19, 10)), '0');
        $bankCode = ltrim(substr($line, 73, 4), '0');
        return new BankAccount($prefix, $number, $bankCode);
    }

    /**
     * @param $number
     * @return string
     */
    private function decodeAccountNumber($number)
    {
        return $number[4] . $number[5] . $number[6] . $number[7] . $number[8] . $number[3] . $number[9] . $number[1] . $number[2] . $number[0];
    }
}