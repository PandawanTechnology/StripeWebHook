<?php

namespace PandawanTechnology\StripeWebHook\Tests\Events;

use PandawanTechnology\StripeWebHook\Events\InvoicePaymentFailedEvent;

class InvoicePaymentFailedEventTest extends AbstractInvoiceEventTest
{
    /**
     * @inheritDoc
     */
    protected function getEventClass(): string
    {
        return InvoicePaymentFailedEvent::class;
    }

    /**
     * @inheritDoc
     */
    protected function getType(): string
    {
        return 'invoice.payment_failed';
    }
}
