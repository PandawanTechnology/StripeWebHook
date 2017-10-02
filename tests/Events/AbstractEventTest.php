<?php

namespace PandawanTechnology\StripeWebHook\Tests\Events;

use PandawanTechnology\StripeWebHook\StripeFactoryEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class AbstractEventTest extends TestCase
{
    protected $event;

    protected $parentEvent;
    protected $stripeEvent;

    /**
     * @return string
     */
    abstract protected function getEventClass(): string;

    /**
     * @return string
     */
    abstract protected function getType(): string;

    protected function setUp()
    {
        $this->parentEvent = $this->createMock(StripeFactoryEvent::class);
        $this->stripeEvent = $this->createMock(\Stripe\Event::class);

        $eventClass = $this->getEventClass();

        $this->event = new $eventClass($this->parentEvent, $this->stripeEvent);
    }

    public function testSetException()
    {
        $exception = $this->createMock(HttpException::class);

        $this->parentEvent->expects($this->once())
            ->method('setException')
            ->with($this->equalTo($exception));

        $this->event->setException($exception);
    }

    public function testGetEventId()
    {
        $eventId = 'evt_1B7Q82DC3JmvHFnFFwe58h2A';

        $this->stripeEvent->expects($this->once())
            ->method('__get')
            ->with($this->equalTo('id'))
            ->willReturn($eventId);

        $this->assertSame($eventId, $this->event->getEventId());
    }

    public function testGetApiVersion()
    {
        $version = '2017-08-15';

        $this->stripeEvent->expects($this->once())
            ->method('__get')
            ->with($this->equalTo('api_version'))
            ->willReturn($version);

        $this->assertSame($version, $this->event->getApiVersion());
    }

    public function testGetType()
    {
        $type = $this->getType();

        $this->stripeEvent->expects($this->once())
            ->method('__get')
            ->with($this->equalTo('type'))
            ->willReturn($type);

        $this->assertSame($type, $this->event->getType());
    }

    public function testGetDataObject()
    {
        $data = $this->createMock(\stdClass::class);
        $object = $this->createMock(\stdClass::class);
        $data->object = $object;

        $this->stripeEvent->expects($this->once())
            ->method('__get')
            ->with($this->equalTo('data'))
            ->willReturn($data);

        $this->assertSame($object, $this->event->getDataObject());
    }

    public function testGetCreated()
    {
        $created = time();

        $this->stripeEvent->expects($this->once())
            ->method('__get')
            ->with($this->equalTo('created'))
            ->willReturn($created);

        $this->assertSame($created, $this->event->getCreated());
    }

    public function testIsLivemode()
    {
        $this->stripeEvent->expects($this->once())
            ->method('__get')
            ->with($this->equalTo('livemode'))
            ->willReturn(false);

        $this->assertFalse($this->event->isLivemode());
    }

    public function testGetPendingWebHooks()
    {
        $pendingWebHooks = 42;

        $this->stripeEvent->expects($this->once())
            ->method('__get')
            ->with($this->equalTo('pending_webhooks'))
            ->willReturn($pendingWebHooks);

        $this->assertSame($pendingWebHooks, $this->event->getPendingWebHooks());
    }

    public function testGetRequestId()
    {
        $requestId = 'req_AVTG91VbrCADo3';

        $this->stripeEvent->expects($this->once())
            ->method('__get')
            ->with($this->equalTo('request'))
            ->willReturn($requestId);

        $this->assertSame($requestId, $this->event->getRequestId());
    }
}
