<?php

namespace JakubZapletal\Component\BankStatement\Statement;

use JakubZapletal\Component\BankStatement\Statement\Transaction\TransactionInterface;

class Statement implements StatementInterface, \Countable, \Iterator
{

    /**
     * @var BankAccount
     */
    protected $bankAccount;

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
     * @var \DateTime
     */
    protected $dateCreated;

    /**
     * @var \DateTime
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
        $this->balance = (float)$balance;

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
        $this->creditTurnover = (float)$creditTurnover;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     *
     * @return $this
     */
    public function setDateCreated(\DateTime $dateCreated)
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
        $this->debitTurnover = (float)$debitTurnover;

        return $this;
    }

    /**
     * @return BankAccount
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    /**
     * @param BankAccount $bankAccount
     *
     * @return $this
     */
    public function setBankAccount(BankAccount $bankAccount)
    {
        $this->bankAccount = $bankAccount;

        return $this;
    }

    /**
     * Only for backward compatibility
     * @deprecated
     *
     * @return string
     */
    public function getAccountNumber()
    {
        if ($this->bankAccount === null) {
            return null;
        }
        return $this->bankAccount->getFormatted();
    }

    /**
     * Only for backward compatibility
     * @deprecated
     *
     * @return array
     */
    public function getParsedAccountNumber()
    {
        $parsedAccountNumber = array(
            'prefix' => null,
            'number' => null,
            'bankCode' => null
        );
        if ($this->bankAccount !== null) {
            $parsedAccountNumber['prefix'] = $this->bankAccount->getPrefix();
            $parsedAccountNumber['number'] = $this->bankAccount->getNumber();
            $parsedAccountNumber['bankCode'] = $this->bankAccount->getBankCode();
        }
        return $parsedAccountNumber;
    }

    /**
     * @return \DateTime
     */
    public function getDateLastBalance()
    {
        return $this->dateLastBalance;
    }

    /**
     * @param \DateTime $dateLastBalance
     *
     * @return $this
     */
    public function setDateLastBalance(\DateTime $dateLastBalance)
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
        $this->lastBalance = (float)$lastBalance;

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
