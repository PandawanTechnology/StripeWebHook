<?php

namespace PandawanTechnology\StripeWebHook\Tests\Events;

use PandawanTechnology\StripeWebHook\Events\AbstractInvoiceEvent;
use Stripe\StripeObject;

abstract class AbstractInvoiceEventTest extends AbstractEventTest
{
    /**
     * @var AbstractInvoiceEvent
     */
    protected $event;

    public function testGetCustomerId()
    {
        $customerId = 'cus_ABCD';
        $object = $this->createMock(StripeObject::class);
        $object->expects($this->once())
               ->method('__get')
               ->with($this->equalTo('customer'))
               ->willReturn($customerId);

        $event = $this->getMockBuilder(get_class($this->event))
            ->disableOriginalConstructor()
            ->setMethods(['getDataObject'])
            ->getMock();

        $event->expects($this->once())
            ->method('getDataObject')
            ->willReturn($object);

        $this->assertSame($customerId, $event->getCustomerId());
    }

    public function testGetAmountDue()
    {
        $amountDue = 422;
        $object = $this->createMock(StripeObject::class);
        $object->expects($this->once())
            ->method('__get')
            ->with($this->equalTo('amount_due'))
            ->willReturn($amountDue);

        $event = $this->getMockBuilder(get_class($this->event))
            ->disableOriginalConstructor()
            ->setMethods(['getDataObject'])
            ->getMock();

        $event->expects($this->once())
            ->method('getDataObject')
            ->willReturn($object);

        $this->assertSame($amountDue, $event->getAmountDue());
    }

    public function testGetCurrencyName()
    {
        $object = $this->createMock(StripeObject::class);
        $object->expects($this->once())
               ->method('__get')
               ->with($this->equalTo('currency'))
               ->willReturn('eur');

        $event = $this->getMockBuilder(get_class($this->event))
            ->disableOriginalConstructor()
            ->setMethods(['getDataObject'])
            ->getMock();

        $event->expects($this->once())
            ->method('getDataObject')
            ->willReturn($object);

        $this->assertSame('EUR', $event->getCurrencyName());
    }
}
