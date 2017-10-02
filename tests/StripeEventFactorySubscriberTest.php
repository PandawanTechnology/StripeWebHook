<?php

namespace PandawanTechnology\StripeWebHook\Tests;

use PandawanTechnology\StripeWebHook\Events;
use PandawanTechnology\StripeWebHook\StripeEventFactorySubscriber;
use PandawanTechnology\StripeWebHook\StripeFactoryEvent;
use PHPUnit\Framework\TestCase;
use Stripe\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StripeEventFactorySubscriberTest extends TestCase
{
    /**
     * @var StripeEventFactorySubscriber
     */
    protected $stripeEventFactorySubscriber;

    protected $apiKey;

    protected function setUp()
    {
        $this->apiKey = 'APIKEY';

        $this->stripeEventFactorySubscriber = new StripeEventFactorySubscriber($this->apiKey);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertSame([
            'stripe' => 'onStripeEvent',
        ], StripeEventFactorySubscriber::getSubscribedEvents());
    }

    public function testCallStatic()
    {
        $reflectionMethod = new \ReflectionMethod(get_class($this->stripeEventFactorySubscriber), 'callStatic');
        $reflectionMethod->setAccessible(true);

        $callback = [\Stripe\Stripe::class, 'setApiKey'];
        $params = [$this->apiKey];

        $this->assertSame(call_user_func_array($callback, $params), $reflectionMethod->invoke($this->stripeEventFactorySubscriber, $callback, $params));
    }

    public function testOnStripeEventNoPayload()
    {
        $event = $this->createStripeEventFactoryEventMock();
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $event->expects($this->once())
            ->method('getRequestContent')
            ->willReturn('');

        $event->expects($this->once())
            ->method('setException')
            ->with($this->isInstanceOf(BadRequestHttpException::class));

        $this->stripeEventFactorySubscriber->onStripeEvent($event, 'stripe', $eventDispatcher);
    }

    public function testOnStripeEventUnexpectedValueException()
    {
        $event = $this->createStripeEventFactoryEventMock();
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $rawPayload = '{}';
        $stripeServerRequestSignature = 'sig';
        $stripeSignature = 'STRIPE_SIG';

        $event->expects($this->once())
            ->method('getRequestContent')
            ->willReturn($rawPayload);
        $event->expects($this->once())
            ->method('getStripeServerRequestSignature')
            ->willReturn($stripeServerRequestSignature);
        $event->expects($this->once())
            ->method('getStripeSignature')
            ->willReturn($stripeSignature);

        $stripeEventFactorySubscriber = $this->getMockBuilder(get_class($this->stripeEventFactorySubscriber))
            ->setConstructorArgs([$this->apiKey])
            ->setMethods(['callStatic'])
            ->getMock();

        $stripeEventFactorySubscriber->expects($this->at(0))
            ->method('callStatic')
            ->with(
                $this->equalTo([\Stripe\Stripe::class, 'setApiKey']),
                $this->equalTo([$this->apiKey])
            );
        $stripeEventFactorySubscriber->expects($this->at(1))
            ->method('callStatic')
            ->with(
                $this->equalTo([
                    \Stripe\Webhook::class,
                    'constructEvent',
                ]),
                $this->equalTo([
                    $rawPayload,
                    $stripeServerRequestSignature,
                    $stripeSignature,
                ])
            )
            ->will($this->throwException(new \UnexpectedValueException()));

        $event->expects($this->once())
            ->method('setException')
            ->with($this->isInstanceOf(BadRequestHttpException::class));

        $stripeEventFactorySubscriber->onStripeEvent($event, 'stripe', $eventDispatcher);
    }

    public function testOnStripeEventSignatureVerificationException()
    {
        $event = $this->createStripeEventFactoryEventMock();
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $rawPayload = '{}';
        $stripeServerRequestSignature = 'sig';
        $stripeSignature = 'STRIPE_SIG';

        $event->expects($this->once())
            ->method('getRequestContent')
            ->willReturn($rawPayload);
        $event->expects($this->once())
            ->method('getStripeServerRequestSignature')
            ->willReturn($stripeServerRequestSignature);
        $event->expects($this->once())
            ->method('getStripeSignature')
            ->willReturn($stripeSignature);

        $stripeEventFactorySubscriber = $this->getMockBuilder(get_class($this->stripeEventFactorySubscriber))
            ->setConstructorArgs([$this->apiKey])
            ->setMethods(['callStatic'])
            ->getMock();

        $stripeEventFactorySubscriber->expects($this->at(0))
            ->method('callStatic')
            ->with(
                $this->equalTo([\Stripe\Stripe::class, 'setApiKey']),
                $this->equalTo([$this->apiKey])
            );
        $stripeEventFactorySubscriber->expects($this->at(1))
            ->method('callStatic')
            ->with(
                $this->equalTo([
                    \Stripe\Webhook::class,
                    'constructEvent',
                ]),
                $this->equalTo([
                    $rawPayload,
                    $stripeServerRequestSignature,
                    $stripeSignature,
                ])
            )
            ->will($this->throwException(new \Stripe\Error\SignatureVerification("Unable to extract timestamp and signatures from header", $stripeServerRequestSignature, $rawPayload)));

        $event->expects($this->once())
            ->method('setException')
            ->with($this->isInstanceOf(BadRequestHttpException::class));

        $stripeEventFactorySubscriber->onStripeEvent($event, 'stripe', $eventDispatcher);
    }

    public function testOnStripeEventInvalidEventName()
    {
        $event = $this->createStripeEventFactoryEventMock();
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $rawPayload = '{}';
        $stripeServerRequestSignature = 'sig';
        $stripeSignature = 'STRIPE_SIG';
        $stripeEvent = $this->createMock(Event::class);
        $invalidEventName = 'some-random-event-name';

        $event->expects($this->once())
            ->method('getRequestContent')
            ->willReturn($rawPayload);
        $event->expects($this->once())
            ->method('getStripeServerRequestSignature')
            ->willReturn($stripeServerRequestSignature);
        $event->expects($this->once())
            ->method('getStripeSignature')
            ->willReturn($stripeSignature);

        $stripeEventFactorySubscriber = $this->getMockBuilder(get_class($this->stripeEventFactorySubscriber))
            ->setConstructorArgs([$this->apiKey])
            ->setMethods(['callStatic'])
            ->getMock();

        $stripeEventFactorySubscriber->expects($this->at(0))
            ->method('callStatic')
            ->with(
                $this->equalTo([\Stripe\Stripe::class, 'setApiKey']),
                $this->equalTo([$this->apiKey])
            );
        $stripeEventFactorySubscriber->expects($this->at(1))
            ->method('callStatic')
            ->with(
                $this->equalTo([
                    \Stripe\Webhook::class,
                    'constructEvent',
                ]),
                $this->equalTo([
                    $rawPayload,
                    $stripeServerRequestSignature,
                    $stripeSignature,
                ])
            )
            ->willReturn($stripeEvent);

        $event->expects($this->once())
            ->method('setException')
            ->with($this->isInstanceOf(BadRequestHttpException::class));

        $stripeEventFactorySubscriber->onStripeEvent($event, $invalidEventName, $eventDispatcher);
    }

    /**
     * @dataProvider dataProviderOnStripeEvent
     *
     * @param string $targetEventName
     * @param string $expectedEventClass
     */
    public function testOnStripeEvent(string $targetEventName, string $expectedEventClass)
    {
        $event = $this->createStripeEventFactoryEventMock();
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $rawPayload = '{}';
        $stripeServerRequestSignature = 'sig';
        $stripeSignature = 'STRIPE_SIG';
        $stripeEvent = $this->createMock(Event::class);

        $event->expects($this->once())
            ->method('getRequestContent')
            ->willReturn($rawPayload);
        $event->expects($this->once())
            ->method('getStripeServerRequestSignature')
            ->willReturn($stripeServerRequestSignature);
        $event->expects($this->once())
            ->method('getStripeSignature')
            ->willReturn($stripeSignature);

        $stripeEventFactorySubscriber = $this->getMockBuilder(get_class($this->stripeEventFactorySubscriber))
            ->setConstructorArgs([$this->apiKey])
            ->setMethods(['callStatic'])
            ->getMock();

        $stripeEventFactorySubscriber->expects($this->at(0))
            ->method('callStatic')
            ->with(
                $this->equalTo([\Stripe\Stripe::class, 'setApiKey']),
                $this->equalTo([$this->apiKey])
            );
        $stripeEventFactorySubscriber->expects($this->at(1))
            ->method('callStatic')
            ->with(
                $this->equalTo([
                    \Stripe\Webhook::class,
                    'constructEvent',
                ]),
                $this->equalTo([
                    $rawPayload,
                    $stripeServerRequestSignature,
                    $stripeSignature,
                ])
            )
            ->willReturn($stripeEvent);

        $event->expects($this->once())
            ->method('getEventName')
            ->willReturn($targetEventName);

        $event->expects($this->never())
            ->method('setException')
            ->with($this->isInstanceOf(BadRequestHttpException::class));

        $eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->equalTo($targetEventName),
                $this->equalTo(new $expectedEventClass($event, $stripeEvent))
            );

        $stripeEventFactorySubscriber->onStripeEvent($event, $targetEventName, $eventDispatcher);
    }

    /**
     * @return array
     */
    public function dataProviderOnStripeEvent(): array
    {
        return [
            ['account.updated', Events\AccountUpdatedEvent::class],
            ['account.application.deauthorized', Events\AccountApplicationDeauthorizedEvent::class],
            ['account.external_account.created', Events\AccountExternalAccountCreatedEvent::class],
            ['account.external_account.deleted', Events\AccountExternalAccountDeletedEvent::class],
            ['account.external_account.updated', Events\AccountExternalAccountUpdatedEvent::class],

            ['application_fee.created', Events\ApplicationFeeCreatedEvent::class],
            ['application_fee.refunded', Events\ApplicationFeeRefundedEvent::class],
            ['application_fee.refund.updated', Events\ApplicationFeeRefundUpdatedEvent::class],

            ['balance.available', Events\BalanceAvailableEvent::class],

            ['charge.captured', Events\ChargeCapturedEvent::class],
            ['charge.failed', Events\ChargeFailedEvent::class],
            ['charge.pending', Events\ChargePendingEvent::class],
            ['charge.refunded', Events\ChargeRefundedEvent::class],
            ['charge.succeeded', Events\ChargeSucceededEvent::class],
            ['charge.dispute.closed', Events\ChargeDisputeClosedEvent::class],
            ['charge.dispute.created', Events\ChargeDisputeCreatedEvent::class],
            ['charge.dispute.funds_reinstated', Events\ChargeDisputeFundsReinstatedEvent::class],
            ['charge.dispute.funds_withdrawn', Events\ChargeDisputeFundsWithdrawnEvent::class],
            ['charge.dispute.updated', Events\ChargeDisputeUpdatedEvent::class],
            ['charge.refund.updated', Events\ChargeRefundUpdatedEvent::class],

            ['coupon.created', Events\CouponCreatedEvent::class],
            ['coupon.deleted', Events\CouponDeletedEvent::class],
            ['coupon.updated', Events\CouponUpdatedEvent::class],

            ['customer.created', Events\CustomerCreatedEvent::class],
            ['customer.deleted', Events\CustomerDeletedEvent::class],
            ['customer.updated', Events\CustomerUpdatedEvent::class],
            ['customer.discount.created', Events\CustomerDiscountCreatedEvent::class],
            ['customer.discount.deleted', Events\CustomerDiscountDeletedEvent::class],
            ['customer.discount.updated', Events\CustomerDiscountUpdatedEvent::class],
            ['customer.source.created', Events\CustomerSourceCreatedEvent::class],
            ['customer.source.deleted', Events\CustomerSourceDeletedEvent::class],
            ['customer.source.updated', Events\CustomerSourceUpdatedEvent::class],
            ['customer.subscription.created', Events\CustomerSubscriptionCreatedEvent::class],
            ['customer.subscription.deleted', Events\CustomerSubscriptionDeletedEvent::class],
            ['customer.subscription.trial_will_end', Events\CustomerSubscriptionTrialWillEndEvent::class],
            ['customer.subscription.updated', Events\CustomerSubscriptionUpdatedEvent::class],

            ['invoice.created', Events\InvoiceCreatedEvent::class],
            ['invoice.payment_failed', Events\InvoicePaymentFailedEvent::class],
            ['invoice.payment_succeeded', Events\InvoicePaymentSucceededEvent::class],
            ['invoice.sent', Events\InvoiceSentEvent::class],
            ['invoice.upcoming', Events\InvoiceUpcomingEvent::class],
            ['invoice.updated', Events\InvoiceUpdatedEvent::class],

            ['invoiceitem.created', Events\InvoiceItemCreatedEvent::class],
            ['invoiceitem.deleted', Events\InvoiceItemDeletedEvent::class],
            ['invoiceitem.updated', Events\InvoiceItemUpdatedEvent::class],

            ['order.created', Events\OrderCreatedEvent::class],
            ['order.payment_failed', Events\OrderPaymentFailedEvent::class],
            ['order.payment_succeeded', Events\OrderPaymentSucceededEvent::class],
            ['order.updated', Events\OrderUpdatedEvent::class],

            ['order_return.created', Events\OrderReturnCreatedEvent::class],

            ['payout.canceled', Events\PayoutCanceledEvent::class],
            ['payout.created', Events\PayoutCreatedEvent::class],
            ['payout.failed', Events\PayoutFailedEvent::class],
            ['payout.paid', Events\PayoutPaidEvent::class],
            ['payout.updated', Events\PayoutUpdatedEvent::class],

            ['plan.created', Events\PlanCreatedEvent::class],
            ['plan.deleted', Events\PlanDeletedEvent::class],
            ['plan.updated', Events\PlanUpdatedEvent::class],

            ['product.created', Events\ProductCreatedEvent::class],
            ['product.deleted', Events\ProductDeletedEvent::class],
            ['product.updated', Events\ProductUpdatedEvent::class],

            ['recipient.created', Events\RecipientCreatedEvent::class],
            ['recipient.deleted', Events\RecipientDeletedEvent::class],
            ['recipient.updated', Events\RecipientUpdatedEvent::class],

            ['review.closed', Events\ReviewClosedEvent::class],
            ['review.opened', Events\ReviewOpenedEvent::class],

            ['sku.created', Events\SkuCreatedEvent::class],
            ['sku.deleted', Events\SkuDeletedEvent::class],
            ['sku.updated', Events\SkuUpdatedEvent::class],

            ['source.canceled', Events\SourceCanceledEvent::class],
            ['source.chargeable', Events\SourceChargeableEvent::class],
            ['source.failed', Events\SourceFailedEvent::class],
            ['source.transaction.created', Events\SourceTransactionCreatedEvent::class],

            ['transfer.created', Events\TransferCreatedEvent::class],
            ['transfer.reversed', Events\TransferReversedEvent::class],
            ['transfer.updated', Events\TransferUpdatedEvent::class],

            ['ping', Events\PingEvent::class],
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createStripeEventFactoryEventMock()
    {
        return $this->createMock(StripeFactoryEvent::class);
    }
}
