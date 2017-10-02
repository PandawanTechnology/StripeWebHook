<?php

namespace PandawanTechnology\StripeWebHook\Tests\Events;

use PandawanTechnology\StripeWebHook\Events\AccountApplicationDeauthorizedEvent;

class AccountApplicationDeauthorizedEventTest extends AbstractEventTest
{
    /**
     * @inheritDoc
     */
    protected function getEventClass(): string
    {
        return AccountApplicationDeauthorizedEvent::class;
    }

    /**
     * @inheritDoc
     */
    protected function getType(): string
    {
        return 'account.application.deauthorized';
    }
}
