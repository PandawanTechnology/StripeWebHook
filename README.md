# pandawan-technology/stripe-web-hook
This library intends to ease the Stripe WebHooks behaviors using the `symfony/event-dispatcher` library.

# Installation
In order to install the library, you should simple use the following command:
```bash
$ composer require pandawan-technology/stripe-web-hook
```

# Usage
To have a better overview of how the library works, let's implement the example provided by Stripe on how to use webhooks [to send an email for failed payments](https://stripe.com/docs/recipes/sending-emails-for-failed-payments)

In our controller, we should have something similar as:
```php
        $endpointSecret = ''; // See the doc to get the correct value
        $event = new StripeFactoryEvent($request, Events::INVOICE_PAYMENT_FAILED, $endpointSignature);
        $eventDispatcher->dispatch(Events::STRIPE_WEBHOOK, $event);

        if ($exception = $event->getException()) {
            throw $exception;
        }

        return new Response();
```
Now, we need to create our EventSubscriber:
```php
<?php

namespace App\EventSubscriber;

use PandawanTechnology\StripeWebHook\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

// For demo purpose
use App\Repository\CustomerRepository;
use SomeMailerInterface;

class InvoicePaymentFailedEvent implements EventSubscriberInterface
{
    /**
     * @return CustomerRepository
     */
    protected $customerRepository;
    
    /**
     * @return SomeMailerInterface
     */
    protected $mailer;
    
    public function __construct(CustomerRepository $customerRepository, SomeMailerInterface $mailer) 
    {
        $this->customerRepository = $customerRepository;
        $this->mailer = $mailer;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() 
    {
        return [
            Events::INVOICE_PAYMENT_FAILED => 'onInvoicePaymentFailed',            
        ];
    }
    
    public function onInvoicePaymentFailed(InvoicePaymentFailedEvent $event, string $eventName, EventDispatcherInterface $eventDispatcher)
    {
        if (!$customer = $this->customerRepository->find($event->getCustomerId())) {
            $event->setException(new NotFoundHttpException(sprintf('No customer with Stripe ID "%s"', $event->getCustomerId())));
        }
        
        $this->mailer->send(
            'myapp@demo.com', // From
            [$customer->getEmail() => $customer->getName()], // To
            'Invoice payment failure', // Subject
            sprintf('Payment for %d%s failed', $event->getAmountDue(), $event->getCurrencyName())
        );
    }
}
```