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
use Thelia\Model\ModuleQuery;
use Thelia\Module\BaseModule;
use Thelia\Module\DeliveryModuleInterface;

/**
 * Class FlatFeeDelivery
 * @package FlatFeeDelivery
 * @author Thelia <info@thelia.net>
 */
class FlatFeeDelivery extends BaseModule implements DeliveryModuleInterface
{

    const STATUS_SENT=4;
    /**
     * calculate and return delivery price
     *
     * @param Country $country
     * @throws \Exception
     *
     * @return mixed
     */
    public function getPostage(Country $country)
    {
        if($country !== null && $country->getArea() !== null) {
            $postage = $country->getArea()->getPostage();
        } else {
            throw new \InvalidArgumentException("Country or Area should not be null");
        }

        return $postage === null ? 0:$postage;
    }

    public static function getModCode() {
        return ModuleQuery::create()->findOneByCode("FlatFeeDelivery")->getId();
    }

    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con->getWrappedConnection());

        $database->insertSql(null, array(__DIR__."/Config/thelia.sql"));
    }
}
