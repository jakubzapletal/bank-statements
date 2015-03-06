<?php

namespace JakubZapletal\Component\BankStatement\Statement;

/**
 * Class BankAccount
 * @package JakubZapletal\Component\BankStatement\Statement
 */
class BankAccount
{

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $bankCode;

    /**
     * @param string|null $prefix
     * @param string|null $number
     * @param string|null $bankCode
     */
    function __construct($prefix = null, $number = null, $bankCode = null)
    {
        $this->prefix = $prefix;
        $this->number = $number;
        $this->bankCode = $bankCode;
    }

    /**
     * @return int
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param $number
     *
     * @return $this
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return int
     */
    public function getBankCode()
    {
        return $this->bankCode;
    }

    /**
     * @param $bankCode
     *
     * @return $this
     */
    public function setBankCode($bankCode)
    {
        $this->bankCode = $bankCode;
        return
            $this;
    }

    /**
     * @return string
     */
    public function getFormatted()
    {
        $formatted = sprintf('%s-%s/%s', $this->prefix, $this->number, $this->bankCode);
        $formatted = ltrim($formatted, '-');
        $formatted = rtrim($formatted, '/');
        return trim($formatted);
    }

    /**
     * @param $accountNumber
     * @return $this
     */
    public function setFormatted($accountNumber)
    {
        $splitBankCode = explode('/', $accountNumber);
        if (count($splitBankCode) === 2) {
            $this->bankCode = $splitBankCode[1];
        }
        $splitNumber = explode('-', $splitBankCode[0]);
        if (count($splitNumber) === 2) {
            $this->prefix = $splitNumber[0];
            $this->number = $splitNumber[1];
        } else {
            if (strlen($splitNumber[0]) <= 10) {
                $this->number = $splitNumber[0];
            } else {
                $this->prefix = substr($splitNumber[0], 0, strlen($splitNumber[0]) - 10);
                $this->number = substr($splitNumber[0], -10, 10);
            }
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return (empty($this->prefix) && empty($this->number) && empty($this->bankCode));
    }

}