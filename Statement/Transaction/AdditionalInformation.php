<?php

namespace JakubZapletal\Component\BankStatement\Statement\Transaction;

use DateTimeImmutable;

class AdditionalInformation implements AdditionalInformationInterface
{
    /**
     * @var string
     */
    protected $transferIdentificationNumber;

    /**
     * @var DateTimeImmutable
     */
    protected $deductionDate;

    /**
     * @var string
     */
    protected $counterPartyName;

    public function getTransferIdentificationNumber(): string
    {
        return $this->transferIdentificationNumber;
    }

    public function setTransferIdentificationNumber(string $transferIdentificationNumber): void
    {
        $this->transferIdentificationNumber = $transferIdentificationNumber;
    }

    public function getDeductionDate(): DateTimeImmutable
    {
        return $this->deductionDate;
    }

    public function setDeductionDate(DateTimeImmutable $deductionDate): void
    {
        $this->deductionDate = $deductionDate;
    }

    public function getCounterPartyName(): string
    {
        return $this->counterPartyName;
    }

    public function setCounterPartyName(string $counterPartyName): void
    {
        $this->counterPartyName = $counterPartyName;
    }
}
