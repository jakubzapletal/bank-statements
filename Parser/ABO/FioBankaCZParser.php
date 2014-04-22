<?php

namespace JakubZapletal\Component\BankStatement\Parser\ABO;

use JakubZapletal\Component\BankStatement\Parser\Parser;

class FioBankaCZParser extends Parser
{
    const POSTING_CODE_DEBIT           = 1;
    const POSTING_CODE_CREDIT          = 2;
    const POSTING_CODE_DEBIT_REVERSAL  = 4;
    const POSTING_CODE_CREDIT_REVERSAL = 5;
} 