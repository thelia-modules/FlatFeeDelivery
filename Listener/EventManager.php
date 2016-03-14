<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace FlatFeeDelivery\Listener;

use FlatFeeDelivery\FlatFeeDelivery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Mailer\MailerFactory;

class EventManager extends BaseAction implements EventSubscriberInterface
{
    /**
     * @var MailerFactory
     */
    protected $mailer;

    public function __construct(MailerFactory $mailer)
    {
        $this->mailer = $mailer;
    }

    /*
     * @params OrderEvent $order
     * Checks if order delivery module is icirelais and if order new status is sent, send an email to the customer.
     */
    public function sendDeliveryNotification(OrderEvent $event)
    {
        if ($event->getOrder()->getDeliveryModuleId() === FlatFeeDelivery::getModuleId()) {
            $order = $event->getOrder();

            if ($order->isSent()) {
                $this->mailer->sendEmailToCustomer(
                    'order_confirmation_flatfeedelivery',
                    $order->getCustomer(),
                    [
                        'order_id' => $order->getId(),
                        'order_ref' => $order->getRef()
                    ]
                );
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_UPDATE_STATUS => array("sendDeliveryNotification", 128)
        );
    }
}
