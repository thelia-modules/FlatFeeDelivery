<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace FlatFeeDelivery\Listener;

use FlatFeeDelivery\FlatFeeDelivery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Mailer\MailerFactory;
use Thelia\Core\Template\ParserInterface;
use Thelia\Model\ConfigQuery;
use Thelia\Model\MessageQuery;

/**
 * Class SendEMail
 * @package FlatFeeDelivery\Listener
 */
class SendEMail extends BaseAction implements EventSubscriberInterface
{
    /**
     * @var MailerFactory
     */
    protected $mailer;
    /**
     * @var ParserInterface
     */
    protected $parser;

    public function __construct(ParserInterface $parser,MailerFactory $mailer)
    {
        $this->parser = $parser;
        $this->mailer = $mailer;
    }

    /**
     * @return \Thelia\Mailer\MailerFactory
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * @params OrderEvent $order
     * Checks if order delivery module is FlatFeeDelivery and if order new status is sent, send an email to the customer.
     */
    public function update_status(OrderEvent $event)
    {
        if ($event->getOrder()->getDeliveryModuleId() === FlatFeeDelivery::getModCode()) {

            if ($event->getOrder()->isSent()) {
                $contact_email = ConfigQuery::read('store_email');

                if ($contact_email) {
                    $message = MessageQuery::create()
                        ->filterByName('order_confirmation_flatfeedelivery')
                        ->findOne();

                    if (false === $message) {
                        throw new \Exception("Failed to load message 'order_confirmation_flatfeedelivery'.");
                    }

                    $order = $event->getOrder();
                    $customer = $order->getCustomer();

                    $this->parser->assign('order_id', $order->getId());
                    $this->parser->assign('order_ref', $order->getRef());

                    $message
                        ->setLocale($order->getLang()->getLocale());

                    $instance = \Swift_Message::newInstance()
                        ->addTo($customer->getEmail(), $customer->getFirstname()." ".$customer->getLastname())
                        ->addFrom($contact_email, ConfigQuery::read('store_name'))
                    ;

                    // Build subject and body
                    $message->buildMessage($this->parser, $instance);

                    $this->getMailer()->send($instance);

                }
            }
        }

    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_UPDATE_STATUS => array("update_status", 128)
        );
    }

}
