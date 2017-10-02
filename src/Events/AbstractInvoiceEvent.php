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
        return (string) $this->getDataObject()->customer;
    }

    /**
     * @return int
     */
    public function getAmountDue(): int
    {
        return (int) $this->getDataObject()->amount_due;
    }

    /**
     * @return string
     */
    public function getCurrencyName(): string
    {
        return mb_strtoupper((string) $this->getDataObject()->currency);
    }
}
