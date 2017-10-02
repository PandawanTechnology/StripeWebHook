<?php

declare(strict_types=1);

namespace PandawanTechnology\StripeWebHook;

/**
 * List the possible events sent by the Stripe' API.
 *
 * @see https://stripe.com/docs/api#event_types
 *
 * @codeCoverageIgnore
 */
final class Events
{
    const STRIPE_WEBHOOK = 'stripe';

    const ACCOUNT_UPDATED = 'account.updated';
    const ACCOUNT_APPLICATION_DEAUTHORIZED = 'account.application.deauthorized';
    const ACCOUNT_EXTERNAL_ACCOUNT_CREATED = 'account.external_account.created';
    const ACCOUNT_EXTERNAL_ACCOUNT_DELETED = 'account.external_account.deleted';
    const ACCOUNT_EXTERNAL_ACCOUNT_UDPATED = 'account.external_account.updated';

    const APPLICATION_FEE_CREATED = 'application_fee.created';
    const APPLICATION_FEE_REFUNDED = 'application_fee.refunded';
    const APPLICATION_FEE_REFUND_UPDATED = 'application_fee.refund.updated';

    const BALANCE_AVAILABLE = 'balance.available';

    const CHARGE_CAPTURED = 'charge.captured';
    const CHARGE_FAILED = 'charge.failed';
    const CHARGE_PENDING = 'charge.pending';
    const CHARGE_REFUNDED = 'charge.refunded';
    const CHARGE_SUCCEEDED = 'charge.succeeded';
    const CHARGE_UPDATED = 'charge.updated';
    const CHARGE_DISPUTE_CLOSED = 'charge.dispute.closed';
    const CHARGE_DISPUTE_CREATED = 'charge.dispute.created';
    const CHARGE_DISPUTE_FUNDS_REINSTATED = 'charge.dispute.funds_reinstated';
    const CHARGE_DISPUTE_FUNDS_WITHDRAWN = 'charge.dispute.funds_withdrawn';
    const CHARGE_DISPUTE_UPDATED = 'charge.dispute.updated';
    const CHARGE_REFUND_UPDATED = 'charge.refund.updated';

    const COUPON_CREATED = 'coupon.created';
    const COUPON_DELETED = 'coupon.deleted';
    const COUPON_UPDATED = 'coupon.updated';

    const CUSTOMER_CREATED = 'customer.created';
    const CUSTOMER_DELETED = 'customer.deleted';
    const CUSTOMER_UPDATED = 'customer.updated';
    const CUSTOMER_DISCOUNT_CREATED = 'customer.discount.created';
    const CUSTOMER_DISCOUNT_DELETED = 'customer.discount.deleted';
    const CUSTOMER_DISCOUNT_UPDATED = 'customer.discount.updated';
    const CUSTOMER_SOURCE_CREATED = 'customer.source.created';
    const CUSTOMER_SOURCE_DELETED = 'customer.source.deleted';
    const CUSTOMER_SOURCE_UPDATED = 'customer.source.updated';
    const CUSTOMER_SUBSCRIPTION_CREATED = 'customer.subscription.created';
    const CUSTOMER_SUBSCRIPTION_DELETED = 'customer.subscription.deleted';
    const CUSTOMER_SUBSCRIPTION_TRIAL_WILL_END = 'customer.subscription.trial_will_end';
    const CUSTOMER_SUBSCRIPTION_UPDATED = 'customer.subscription.updated';

    const INVOICE_CREATED = 'invoice.created';
    const INVOICE_PAYMENT_FAILED = 'invoice.payment_failed';
    const INVOICE_PAYMENT_SUCCEEDED = 'invoice.payment_succeeded';
    const INVOICE_SENT = 'invoice.sent';
    const INVOICE_UPCOMING = 'invoice.upcoming';
    const INVOICE_UPDATED = 'invoice.updated';

    const INVOICEITEM_CREATED = 'invoiceitem.created';
    const INVOICEITEM_DELETED = 'invoiceitem.deleted';
    const INVOICEITEM_UPDATED = 'invoiceitem.updated';

    const ORDER_CREATED = 'order.created';
    const ORDER_PAYMENT_FAILED = 'order.payment_failed';
    const ORDER_PAYMENT_SUCCEEDED = 'order.payment_succeeded';
    const ORDER_UPDATED = 'order.updated';

    const ORDER_RETURN_CREATED = 'order_return.created';

    const PAYOUT_CANCELED = 'payout.canceled';
    const PAYOUT_CREATED = 'payout.created';
    const PAYOUT_FAILED = 'payout.failed';
    const PAYOUT_PAID = 'payout.paid';
    const PAYOUT_UPDATED = 'payout.updated';

    const PLAN_CREATED = 'plan.created';
    const PLAN_DELETED = 'plan.deleted';
    const PLAN_UPDATED = 'plan.updated';

    const PRODUCT_CREATED = 'product.created';
    const PRODUCT_DELETED = 'product.deleted';
    const PRODUCT_UPDATED = 'product.updated';

    const RECIPIENT_CREATED = 'recipient.created';
    const RECIPIENT_DELETED = 'recipient.deleted';
    const RECIPIENT_UPDATED = 'recipient.updated';

    const REVIEW_CLOSED = 'review.closed';
    const REVIEW_OPENED = 'review.opened';

    const SKU_CREATED = 'sku.created';
    const SKU_DELETED = 'sku.deleted';
    const SKU_UPDATED = 'sku.updated';

    const SOURCE_CANCELED = 'source.canceled';
    const SOURCE_CHARGEABLE = 'source.chargeable';
    const SOURCE_FAILED = 'source.failed';
    const SOURCE_TRANSACTION_CREATED = 'source.transaction.created';

    const TRANSFER_CREATED = 'transfer.created';
    const TRANSFER_REVERSED = 'transfer.reversed';
    const TRANSFER_UPDATED = 'transfer.updated';

    const PING = 'ping';

    private function __construct()
    {
    }
}
