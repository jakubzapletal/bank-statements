<?php

namespace JakubZapletal\Component\BankStatement\Statement;

class ABOStatement extends Statement
{
    /**
     * @var \DateTime
     */
    protected $dateLastBalance;

    /**
     * @var float
     */
    protected $lastBalance;

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
        $this->lastBalance = (float) $lastBalance;

        return $this;
    }
}