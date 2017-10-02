<?php

declare(strict_types=1);

namespace PandawanTechnology\StripeWebHook;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StripeEventFactorySubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $stripeApiKey;

    /**
     * @param string $stripeApiKey
     */
    public function __construct(string $stripeApiKey)
    {
        $this->stripeApiKey = $stripeApiKey;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::STRIPE_WEBHOOK => 'onStripeEvent',
        ];
    }

    /**
     * @param StripeFactoryEvent       $stripeFactoryEvent
     * @param string                   $eventName
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function onStripeEvent(StripeFactoryEvent $stripeFactoryEvent, string $eventName, EventDispatcherInterface $eventDispatcher)
    {
        if (!$payload = $stripeFactoryEvent->getRequestContent()) {
            $stripeFactoryEvent->setException(new BadRequestHttpException('Unable to read input'));

            return;
        }

        $this->callStatic([\Stripe\Stripe::class, 'setApiKey'], [$this->stripeApiKey]);

        try {
            $stripeEvent = $this->callStatic([\Stripe\Webhook::class, 'constructEvent'], [
                $payload,
                $stripeFactoryEvent->getStripeServerRequestSignature(),
                $stripeFactoryEvent->getStripeSignature(),
            ]);
        } catch (\Exception $e) {
            $stripeFactoryEvent->setException(new BadRequestHttpException($e->getMessage()));

            return;
        }

        if (!$eventClass = $this->getEventClass($eventName)) {
            $stripeFactoryEvent->setException(new BadRequestHttpException(sprintf('Unsupported event "%s"', $eventName)));

            return;
        }

        $eventDispatcher->dispatch($eventName, new $eventClass($stripeFactoryEvent, $stripeEvent));
    }

    /**
     * @param string $eventName
     *
     * @return null|string
     */
    protected function getEventClass(string $eventName): ?string
    {
        switch ($eventName) {
            case Events::ACCOUNT_UPDATED:
                return Events\AccountUpdatedEvent::class;
            case Events::ACCOUNT_APPLICATION_DEAUTHORIZED:
                return Events\AccountApplicationDeauthorizedEvent::class;
            case Events::ACCOUNT_EXTERNAL_ACCOUNT_CREATED:
                return Events\AccountExternalAccountCreatedEvent::class;
            case Events::ACCOUNT_EXTERNAL_ACCOUNT_DELETED:
                return Events\AccountExternalAccountDeletedEvent::class;
            case Events::ACCOUNT_EXTERNAL_ACCOUNT_UDPATED:
                return Events\AccountExternalAccountUpdatedEvent::class;

            case Events::APPLICATION_FEE_CREATED:
                return Events\ApplicationFeeCreatedEvent::class;
            case Events::APPLICATION_FEE_REFUNDED:
                return Events\ApplicationFeeRefundedEvent::class;
            case Events::APPLICATION_FEE_REFUND_UPDATED:
                return Events\ApplicationFeeRefundUpdatedEvent::class;

            case Events::BALANCE_AVAILABLE:
                return Events\BalanceAvailableEvent::class;

            case Events::CHARGE_CAPTURED:
                return Events\ChargeCapturedEvent::class;
            case Events::CHARGE_FAILED:
                return Events\ChargeFailedEvent::class;
            case Events::CHARGE_PENDING:
                return Events\ChargePendingEvent::class;
            case Events::CHARGE_REFUNDED:
                return Events\ChargeRefundedEvent::class;
            case Events::CHARGE_SUCCEEDED:
                return Events\ChargeSucceededEvent::class;
            case Events::CHARGE_DISPUTE_CLOSED:
                return Events\ChargeDisputeClosedEvent::class;
            case Events::CHARGE_DISPUTE_CREATED:
                return Events\ChargeDisputeCreatedEvent::class;
            case Events::CHARGE_DISPUTE_FUNDS_REINSTATED:
                return Events\ChargeDisputeFundsReinstatedEvent::class;
            case Events::CHARGE_DISPUTE_FUNDS_WITHDRAWN:
                return Events\ChargeDisputeFundsWithdrawnEvent::class;
            case Events::CHARGE_DISPUTE_UPDATED:
                return Events\ChargeDisputeUpdatedEvent::class;
            case Events::CHARGE_REFUND_UPDATED:
                return Events\ChargeRefundUpdatedEvent::class;

            case Events::COUPON_CREATED:
                return Events\CouponCreatedEvent::class;
            case Events::COUPON_DELETED:
                return Events\CouponDeletedEvent::class;
            case Events::COUPON_UPDATED:
                return Events\CouponUpdatedEvent::class;

            case Events::CUSTOMER_CREATED:
                return Events\CustomerCreatedEvent::class;
            case Events::CUSTOMER_DELETED:
                return Events\CustomerDeletedEvent::class;
            case Events::CUSTOMER_UPDATED:
                return Events\CustomerUpdatedEvent::class;
            case Events::CUSTOMER_DISCOUNT_CREATED:
                return Events\CustomerDiscountCreatedEvent::class;
            case Events::CUSTOMER_DISCOUNT_DELETED:
                return Events\CustomerDiscountDeletedEvent::class;
            case Events::CUSTOMER_DISCOUNT_UPDATED:
                return Events\CustomerDiscountUpdatedEvent::class;
            case Events::CUSTOMER_SOURCE_CREATED:
                return Events\CustomerSourceCreatedEvent::class;
            case Events::CUSTOMER_SOURCE_DELETED:
                return Events\CustomerSourceDeletedEvent::class;
            case Events::CUSTOMER_SOURCE_UPDATED:
                return Events\CustomerSourceUpdatedEvent::class;
            case Events::CUSTOMER_SUBSCRIPTION_CREATED:
                return Events\CustomerSubscriptionCreatedEvent::class;
            case Events::CUSTOMER_SUBSCRIPTION_DELETED:
                return Events\CustomerSubscriptionDeletedEvent::class;
            case Events::CUSTOMER_SUBSCRIPTION_TRIAL_WILL_END:
                return Events\CustomerSubscriptionTrialWillEndEvent::class;
            case Events::CUSTOMER_SUBSCRIPTION_UPDATED:
                return Events\CustomerSubscriptionUpdatedEvent::class;

            case Events::INVOICE_CREATED:
                return Events\InvoiceCreatedEvent::class;
            case Events::INVOICE_PAYMENT_FAILED:
                return Events\InvoicePaymentFailedEvent::class;
            case Events::INVOICE_PAYMENT_SUCCEEDED:
                return Events\InvoicePaymentSucceededEvent::class;
            case Events::INVOICE_SENT:
                return Events\InvoiceSentEvent::class;
            case Events::INVOICE_UPCOMING:
                return Events\InvoiceUpcomingEvent::class;
            case Events::INVOICE_UPDATED:
                return Events\InvoiceUpdatedEvent::class;

            case Events::INVOICEITEM_CREATED:
                return Events\InvoiceItemCreatedEvent::class;
            case Events::INVOICEITEM_DELETED:
                return Events\InvoiceItemDeletedEvent::class;
            case Events::INVOICEITEM_UPDATED:
                return Events\InvoiceItemUpdatedEvent::class;

            case Events::ORDER_CREATED:
                return Events\OrderCreatedEvent::class;
            case Events::ORDER_PAYMENT_FAILED:
                return Events\OrderPaymentFailedEvent::class;
            case Events::ORDER_PAYMENT_SUCCEEDED:
                return Events\OrderPaymentSucceededEvent::class;
            case Events::ORDER_UPDATED:
                return Events\OrderUpdatedEvent::class;

            case Events::ORDER_RETURN_CREATED:
                return Events\OrderReturnCreatedEvent::class;

            case Events::PAYOUT_CANCELED:
                return Events\PayoutCanceledEvent::class;
            case Events::PAYOUT_CREATED:
                return Events\PayoutCreatedEvent::class;
            case Events::PAYOUT_FAILED:
                return Events\PayoutFailedEvent::class;
            case Events::PAYOUT_PAID:
                return Events\PayoutPaidEvent::class;
            case Events::PAYOUT_UPDATED:
                return Events\PayoutUpdatedEvent::class;

            case Events::PLAN_CREATED:
                return Events\PlanCreatedEvent::class;
            case Events::PLAN_DELETED:
                return Events\PlanDeletedEvent::class;
            case Events::PLAN_UPDATED:
                return Events\PlanUpdatedEvent::class;

            case Events::PRODUCT_CREATED:
                return Events\ProductCreatedEvent::class;
            case Events::PRODUCT_DELETED:
                return Events\ProductDeletedEvent::class;
            case Events::PRODUCT_UPDATED:
                return Events\ProductUpdatedEvent::class;

            case Events::RECIPIENT_CREATED:
                return Events\RecipientCreatedEvent::class;
            case Events::RECIPIENT_DELETED:
                return Events\RecipientDeletedEvent::class;
            case Events::RECIPIENT_UPDATED:
                return Events\RecipientUpdatedEvent::class;

            case Events::REVIEW_CLOSED:
                return Events\ReviewClosedEvent::class;
            case Events::REVIEW_OPENED:
                return Events\ReviewOpenedEvent::class;

            case Events::SKU_CREATED:
                return Events\SkuCreatedEvent::class;
            case Events::SKU_DELETED:
                return Events\SkuDeletedEvent::class;
            case Events::SKU_UPDATED:
                return Events\SkuUpdatedEvent::class;

            case Events::SOURCE_CANCELED:
                return Events\SourceCanceledEvent::class;
            case Events::SOURCE_CHARGEABLE:
                return Events\SourceChargeableEvent::class;
            case Events::SOURCE_FAILED:
                return Events\SourceFailedEvent::class;
            case Events::SOURCE_TRANSACTION_CREATED:
                return Events\SourceTransactionCreatedEvent::class;

            case Events::TRANSFER_CREATED:
                return Events\TransferCreatedEvent::class;
            case Events::TRANSFER_REVERSED:
                return Events\TransferReversedEvent::class;
            case Events::TRANSFER_UPDATED:
                return Events\TransferUpdatedEvent::class;

            case Events::PING:
                return Events\PingEvent::class;
        }

        return null;
    }

    /**
     * @param callable $callable
     * @param array    $args
     *
     * @return mixed
     */
    protected function callStatic(callable $callable, array $args = [])
    {
        return call_user_func_array($callable, $args);
    }
}
