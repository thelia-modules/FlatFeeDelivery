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

namespace FlatFeeDelivery;

use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;
use Thelia\Model\Country;
use Thelia\Model\Message;
use Thelia\Model\MessageQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Module\AbstractDeliveryModule;

/**
 * Class FlatFeeDelivery
 * @package FlatFeeDelivery
 * @author Thelia <info@thelia.net>
 */
class FlatFeeDelivery extends AbstractDeliveryModule
{

    /**
     * The shipping confirmation message identifier
     */
    const CONFIRMATION_MESSAGE_NAME = 'order confirmation_flatfeedelivery';

    /**
     * calculate and return delivery price
     *
     * @param  Country    $country
     * @throws \Exception
     *
     * @return mixed
     */
    public function getPostage(Country $country)
    {
        if (null !== $area = $this->getAreaForCountry($country)) {
            $postage = $area->getPostage();
        } else {
            throw new \InvalidArgumentException("Country or Area should not be null");
        }

        return $postage === null ? 0 : $postage;
    }

    /**
     * This method is called by the Delivery  loop, to check if the current module has to be displayed to the customer.
     * Override it to implements your delivery rules/
     *
     * If you return true, the delivery method will de displayed to the customer
     * If you return false, the delivery method will not be displayed
     *
     * @param Country $country the country to deliver to.
     *
     * @return boolean
     */
    public function isValidDelivery(Country $country)
    {
        // We should find an area for the country.
        return null !== $this->getAreaForCountry($country);
    }

    public function postActivation(ConnectionInterface $con = null)
    {
        // Create payment confirmation message from templates, if not already defined
        $email_templates_dir = __DIR__.DS.'I18n'.DS.'email-templates'.DS;

        if (null === MessageQuery::create()->findOneByName(self::CONFIRMATION_MESSAGE_NAME)) {
            $message = new Message();

            $message
                ->setName(self::CONFIRMATION_MESSAGE_NAME)

                ->setLocale('en_US')
                ->setTitle('Flat rate shipping notification')
                ->setSubject('Your order {$order_ref} has been shipped')
                ->setHtmlMessage(file_get_contents($email_templates_dir.'en.html'))
                ->setTextMessage(file_get_contents($email_templates_dir.'en.txt'))

                ->setLocale('fr_FR')
                ->setTitle('Notification d\'envoi forfaitaire')
                ->setSubject('Votre commande {$order_ref} a été expédiée')
                ->setHtmlMessage(file_get_contents($email_templates_dir.'fr.html'))
                ->setTextMessage(file_get_contents($email_templates_dir.'fr.txt'))

                ->save()
            ;
        }
    }

    public function destroy(ConnectionInterface $con = null, $deleteModuleData = false)
    {
        // Delete our message
        if (null !== $message = MessageQuery::create()->findOneByName(self::CONFIRMATION_MESSAGE_NAME)) {
            $message->delete($con);
        }
    }
}
