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
 
namespace FlatFeeDelivery\Hook;

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\AreaQuery;

class HookManager extends BaseHook
{
    public function processShippingConfigurationEditBottom(HookRenderEvent $event)
    {
        $areaId = $event->getArgument('area_id');

        $area = AreaQuery::create()->findPk($areaId);

        $event->add(
            $this->render(
                "shipping-area-delivery-price.html",
                [
                    'area_id' => $areaId,
                    'postage' => $area === null ? 0 : $area->getPostage()
                ]
            )
        );
    }
}
