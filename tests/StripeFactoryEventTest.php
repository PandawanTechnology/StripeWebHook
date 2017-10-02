<?php

namespace PandawanTechnology\StripeWebHook\Tests;

use PandawanTechnology\StripeWebHook\StripeFactoryEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ServerBag;

class StripeFactoryEventTest extends TestCase
{
    protected $request;
    protected $serverSignature;

    protected function setUp()
    {
        $this->request = $this->createMock(Request::class);
        $this->request->server = $this->createMock(ServerBag::class);
        $this->serverSignature = 'SERVER_SIG';
    }

    public function testGetRequestContent()
    {
        $requestContent = 'ABCD';
        $eventName = 'event';

        $this->request->expects($this->once())
            ->method('getContent')
            ->willReturn($requestContent);

        $this->assertSame($requestContent, $this->createStripeFctoryEvent($eventName)->getRequestContent());
    }

    public function testGetStripeServerRequestSignature()
    {
        $stripeServerSignature = 'ABCD';
        $eventName = 'event';

        $this->request->server->expects($this->once())
            ->method('get')
            ->with($this->equalTo('HTTP_STRIPE_SIGNATURE'))
            ->willReturn($stripeServerSignature);

        $this->assertSame($stripeServerSignature, $this->createStripeFctoryEvent($eventName)->getStripeServerRequestSignature());
    }

    public function testGetEventName()
    {
        $eventName = 'event';

        $this->assertSame($eventName, $this->createStripeFctoryEvent($eventName)->getEventName());
    }

    public function testGetStripeSignature()
    {
        $this->assertSame($this->serverSignature, $this->createStripeFctoryEvent('test')->getStripeSignature());
    }

    /**
     * @param string $eventName
     *
     * @return StripeFactoryEvent
     */
    protected function createStripeFctoryEvent(string $eventName): StripeFactoryEvent
    {
        return new StripeFactoryEvent($this->request, $eventName, $this->serverSignature);
    }
}
