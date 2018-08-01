<?php

namespace JakubZapletal\Component\BankStatement\Statement;

use JakubZapletal\Component\BankStatement\Statement\Transaction\TransactionInterface;

class Statement implements StatementInterface, \Countable, \Iterator
{
    /**
     * @var string
     */
    protected $accountNumber;

    /**
     * @var float
     */
    protected $balance;

    /**
     * @var float
     */
    protected $debitTurnover;

    /**
     * @var float
     */
    protected $creditTurnover;

    /**
     * @var string
     */
    protected $serialNumber;

    /**
     * @var \DateTimeImmutable
     */
    protected $dateCreated;

    /**
     * @var \DateTimeImmutable
     */
    protected $dateLastBalance;

    /**
     * @var float
     */
    protected $lastBalance;

    /**
     * @var TransactionInterface[]
     */
    protected $transactions = array();

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param $balance
     *
     * @return $this
     */
    public function setBalance($balance)
    {
        $this->balance = (float) $balance;

        return $this;
    }

    /**
     * @return float
     */
    public function getCreditTurnover()
    {
        return $this->creditTurnover;
    }

    /**
     * @param $creditTurnover
     *
     * @return $this
     */
    public function setCreditTurnover($creditTurnover)
    {
        $this->creditTurnover = (float) $creditTurnover;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTimeImmutable $dateCreated
     *
     * @return $this
     */
    public function setDateCreated(\DateTimeImmutable $dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * @param $serialNumber
     *
     * @return $this
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * @return float
     */
    public function getDebitTurnover()
    {
        return $this->debitTurnover;
    }

    /**
     * @param $debitTurnover
     *
     * @return $this
     */
    public function setDebitTurnover($debitTurnover)
    {
        $this->debitTurnover = (float) $debitTurnover;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param $accountNumber
     *
     * @return $this
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * Split account number to parts
     *
     * @return array
     */
    public function getParsedAccountNumber()
    {
        $parsedAccountNumber = array(
            'prefix'   => null,
            'number'   => null,
            'bankCode' => null
        );

        $accountNumber = $this->getAccountNumber();

        $splitBankCode = explode('/', $accountNumber);
        if (count($splitBankCode) === 2) {
            $parsedAccountNumber['bankCode'] = $splitBankCode[1];
        }

        $splitNumber = explode('-', $splitBankCode[0]);
        if (count($splitNumber) === 2) {
            $parsedAccountNumber['prefix'] = $splitNumber[0];
            $parsedAccountNumber['number'] = $splitNumber[1];
        } else {
            if (strlen($splitNumber[0]) <= 10) {
                $parsedAccountNumber['number'] = $splitNumber[0];
            } else {
                $parsedAccountNumber['prefix'] = substr($splitNumber[0], 0, strlen($splitNumber[0]) - 10);
                $parsedAccountNumber['number'] = substr($splitNumber[0], -10, 10);
            }
        }

        return $parsedAccountNumber;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateLastBalance()
    {
        return $this->dateLastBalance;
    }

    /**
     * @param \DateTimeImmutable $dateLastBalance
     *
     * @return $this
     */
    public function setDateLastBalance(\DateTimeImmutable $dateLastBalance)
    {
        $this->dateLastBalance = $dateLastBalance;

        return $this;
    }

    /**
     * @return float
     */
    public function getLastBalance()
    {
        return $this->lastBalance;
    }

    /**
     * @param float $lastBalance
     *
     * @return $this
     */
    public function setLastBalance($lastBalance)
    {
        $this->lastBalance = (float) $lastBalance;

        return $this;
    }

    /**
     * @return TransactionInterface[]
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @param TransactionInterface $transaction
     *
     * @return $this
     */
    public function addTransaction(TransactionInterface $transaction)
    {
        $added = false;

        foreach ($this->transactions as $addedTransaction) {
            if ($transaction === $addedTransaction) {
                $added = true;
                break;
            }
        }

        if ($added !== true) {
            $this->transactions[] = $transaction;
        }

        return $this;
    }

    /**
     * @param TransactionInterface $transaction
     *
     * @return $this
     */
    public function removeTransaction(TransactionInterface $transaction)
    {
        foreach ($this->transactions as $key => $addedTransaction) {
            if ($transaction === $addedTransaction) {
                unset($this->transactions[$key]);
                break;
            }
        }

        return $this;
    }

    /**
     * @see \Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return count($this->transactions);
    }

    /**
     * @see \Iterator::current()
     *
     * @return TransactionInterface
     */
    public function current()
    {
        return current($this->transactions);
    }

    /**
     * @see \Iterator::key()
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->transactions);
    }

    /**
     * @see \Iterator::next()
     *
     * @return TransactionInterface
     */
    public function next()
    {
        return next($this->transactions);
    }

    /**
     * @see \Iterator::rewind()
     *
     * @return mixed
     */
    public function rewind()
    {
        return reset($this->transactions);
    }

    /**
     * @see \Iterator::valid()
     *
     * @return bool
     */
    public function valid()
    {
        return key($this->transactions) !== null;
    }
}
