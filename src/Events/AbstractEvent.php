<?php

declare(strict_types=1);

namespace PandawanTechnology\StripeWebHook\Events;

use PandawanTechnology\StripeWebHook\StripeFactoryEvent;
use Stripe\Event as StripeEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class AbstractEvent extends Event
{
    /**
     * @var StripeFactoryEvent
     */
    protected $parent;

    /**
     * @var StripeEvent
     */
    protected $stripeEvent;

    /**
     * @param StripeFactoryEvent $parent
     * @param StripeEvent        $stripeEvent
     */
    public function __construct(StripeFactoryEvent $parent, StripeEvent $stripeEvent)
    {
        $this->parent = $parent;
        $this->stripeEvent = $stripeEvent;
    }

    /**
     * @param HttpException $exception
     */
    public function setException(HttpException $exception)
    {
        $this->parent->setException($exception);
    }

    /**
     * @return string
     */
    public function getEventId(): string
    {
        return $this->stripeEvent->id;
    }

    /**
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this->stripeEvent->api_version;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->stripeEvent->type;
    }

    /**
     * @return int
     */
    public function getCreated(): int
    {
        return $this->stripeEvent->created;
    }

    /**
     * @return \stdClass
     */
    public function getDataObject(): \stdClass
    {
        return $this->stripeEvent->data->object;
    }

    /**
     * @return bool
     */
    public function isLiveMode(): bool
    {
        return $this->stripeEvent->livemode;
    }

    /**
     * @return int
     */
    public function getPendingWebhooks(): int
    {
        return $this->stripeEvent->pending_webhooks;
    }

    /**
     * @return string
     */
    public function getRequestId(): string
    {
        return $this->stripeEvent->request;
    }
}
