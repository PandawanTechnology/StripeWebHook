<?php

namespace PandawanTechnology\StripeWebHook\Tests\Events;

use PandawanTechnology\StripeWebHook\Events\AbstractInvoiceEvent;

abstract class AbstractInvoiceEventTest extends AbstractEventTest
{
    /**
     * @var AbstractInvoiceEvent
     */
    protected $event;

    public function testGetCustomerId()
    {
        $customerId = 'cus_ABCD';
        $object = $this->createMock(\stdClass::class);
        $object->customer = $customerId;

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
        $object = $this->createMock(\stdClass::class);
        $object->amount_due = $amountDue;

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
        $object = $this->createMock(\stdClass::class);
        $object->currency = 'eur';

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
