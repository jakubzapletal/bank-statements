<?php

namespace JakubZapletal\Component\BankStatement\Statement\Transaction;

use DateTimeImmutable;

interface AdditionalInformationInterface
{
    public function getTransferIdentificationNumber(): string;

    public function setTransferIdentificationNumber(string $transferIdentificationNumber): void;

    public function getDeductionDate(): DateTimeImmutable;

    public function setDeductionDate(DateTimeImmutable $deductionDate): void;

    public function getCounterPartyName(): string;

    public function setCounterPartyName(string $counterPartyName): void;
}
