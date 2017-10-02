<?php

declare(strict_types=1);

namespace PandawanTechnology\StripeWebHook\Events;

abstract class AbstractInvoiceEvent extends AbstractEvent
{
    /**
     * @return string
     */
    public function getCustomerId(): string
    {
        return $this->getDataObject()->customer;
    }

    /**
     * @return int
     */
    public function getAmountDue(): int
    {
        return $this->getDataObject()->amount_due;
    }

    /**
     * @return string
     */
    public function getCurrencyName(): string
    {
        return mb_strtoupper($this->getDataObject()->currency);
    }
}
