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

namespace FlatFeeDelivery\Tests;

use FlatFeeDelivery\FlatFeeDelivery;
use Thelia\Model\Area;
use Thelia\Model\Country;

/**
 * Class FlatFeeDeliveryTest
 * @package FlatFeeDelivery\Tests
 * @author Thelia <info@thelia.net>
 */
class FlatFeeDeliveryTest extends \PHPUnit_Framework_TestCase {

    public function testGetPostageWithNullCountry()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $instance = new FlatFeeDelivery();

        // Area === null
        $instance->getPostage(new Country());


    }
    public function testGetPostage()
    {
        $country = new Country();
        $area = new Area();

        $instance = new FlatFeeDelivery();

        $area->setPostage(2.0);
        $country->setArea($area);
        $this->assertEquals($instance->getPostage($country), 2.0);

        $area->setPostage(null);
        $country->setArea($area);
        $this->assertEquals($instance->getPostage($country), 0.0);


    }
}