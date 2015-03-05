<?php

namespace JakubZapletal\Component\BankStatement\Parser\ABO;

use JakubZapletal\Component\BankStatement\Parser\ABOParser;

class TatraBankaSKParser extends ABOParser
{
    protected function parseAccountNumber($line)
    {
        $number = str_pad(substr($line, 3, 10), 10, '0', STR_PAD_LEFT);
        $prefix = ltrim(substr($line, 13, 6), '0');
        return $prefix . $this->decodeAccountNumber($number);
    }

    protected function parseCounterAccountNumber($line)
    {
        $number = str_pad(substr($line, 19, 10), 10, '0', STR_PAD_LEFT);
        $prefix = ltrim(substr($line, 34, 6), '0');
        $codeOfBank = substr($line, 73, 4);
        return $prefix . $this->decodeAccountNumber($number) . '/' . $codeOfBank;
    }

    private function decodeAccountNumber($number)
    {
        return $number[4] . $number[5] . $number[6] . $number[7] . $number[8] . $number[3] . $number[9] . $number[1] . $number[2] . $number[0];
    }
}