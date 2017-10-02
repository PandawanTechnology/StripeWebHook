<?php

declare(strict_types=1);

namespace PandawanTechnology\StripeWebHook;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StripeFactoryEvent extends Event
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $eventName;

    /**
     * @var string
     */
    protected $stripeSignature;

    /**
     * @var null|HttpException
     */
    protected $exception;

    /**
     * @param Request $request
     * @param string  $eventName
     * @param string  $stripeSignature
     */
    public function __construct(Request $request, string $eventName, string $stripeSignature)
    {
        $this->request = $request;
        $this->eventName = $eventName;
        $this->stripeSignature = $stripeSignature;
    }

    /**
     * @return resource|string
     */
    public function getRequestContent()
    {
        return $this->request->getContent();
    }

    /**
     * @return string
     */
    public function getStripeServerRequestSignature(): string
    {
        return $this->request->server->get('HTTP_STRIPE_SIGNATURE');
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * @return string
     */
    public function getStripeSignature(): string
    {
        return $this->stripeSignature;
    }

    /**
     * @return null|HttpException
     *
     * @codeCoverageIgnore
     */
    public function getException(): ?HttpException
    {
        return $this->exception;
    }

    /**
     * @param null|HttpException $exception
     *
     * @codeCoverageIgnore
     */
    public function setException(HttpException $exception = null)
    {
        $this->exception = $exception;
    }
}
