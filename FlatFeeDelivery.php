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

namespace FlatFeeDelivery;

use FlatFeeDelivery\Model\Config\FlatFeeDeliveryConfigValue;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Country;
use Thelia\Model\ModuleQuery;
use Thelia\Model\OrderPostage;
use Thelia\Module\AbstractDeliveryModule;
use Thelia\Module\Exception\DeliveryException;

/**
 * Class FlatFeeDelivery
 * @package FlatFeeDelivery
 * @author Thomas Arnaud <tarnaud@openstudio.fr>
 */
class FlatFeeDelivery extends AbstractDeliveryModule
{
    const DOMAIN_NAME = "flatfeedelivery";

    /**
     * @return int
     */
    public static function getModCode()
    {
        return ModuleQuery::create()->findOneByCode("FlatFeeDelivery")->getId();
    }

    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con);

        $this->initializeConfig();

        $database->insertSql(null, array(__DIR__."/Config/thelia.sql"));
    }

    protected function initializeConfig()
    {
        if (null === FlatFeeDelivery::getConfigValue(FlatFeeDeliveryConfigValue::ENABLED)) {
            FlatFeeDelivery::setConfigValue(FlatFeeDeliveryConfigValue::ENABLED, 0);
        }
    }

    /**
     * This method is called by the Delivery loop, to check if the current module has to be displayed to the customer.
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
        $areas = $country->getAreas();
        $postages = [];
        $isPostageNotNull = false;

        foreach($areas as $area) {
            $postages[] = ConfigQuery::read("flatfeedelivery_" . 'area_postage_' . $area->getId(), "");

            foreach($postages as $postage) {
                if($postage != '') {
                    $isPostageNotNull = true;
                }
            }
        }

        if (0 === intval(self::getConfigValue(FLatFeeDeliveryConfigValue::ENABLED))) {
            return false;
        }

        return $isPostageNotNull;
    }

    /**
     * Calculate and return delivery price in the shop's default currency
     *
     * @param Country $country the country to deliver to.
     *
     * @return OrderPostage|float             the delivery price
     * @throws DeliveryException if the postage price cannot be calculated.
     */
    public function getPostage(Country $country)
    {
        $areas = $country->getAreas();
        $postages = [];

        foreach($areas as $area) {
            $postage = ConfigQuery::read("flatfeedelivery_" . 'area_postage_' . $area->getId(), "");
            if($postage != '') {
                $postages[] = $postage;
            }
        }

        return min($postages);
    }
}
