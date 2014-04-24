<?php

namespace JakubZapletal\Component\BankStatement\Parser\ABO;

use JakubZapletal\Component\BankStatement\Parser\ABOParser;

class CeskaSporitelnaCZParser extends ABOParser
{
    const POSTING_CODE_DEBIT           = 1;
    const POSTING_CODE_CREDIT          = 2;
    const POSTING_CODE_DEBIT_REVERSAL  = 3;
    const POSTING_CODE_CREDIT_REVERSAL = 4;
} 