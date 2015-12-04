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

namespace FlatFeeDelivery\Controller;

use FlatFeeDelivery\FlatFeeDelivery;
use FlatFeeDelivery\Model\Config\FlatFeeDeliveryConfigValue;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\ConfigQuery;
use Thelia\Tools\URL;

/**
 * Class PostageUpdateController
 * @package FlatFeeDelivery\Controller
 * @author Thomas Arnaud <tarnaud@openstudio.fr>
 */
class PostageUpdateController extends BaseAdminController
{

    public function updatePostageAction()
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, [FlatFeeDelivery::DOMAIN_NAME], AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm('flat.fee.delivery.postage.update');
        $error_message = null;

        try {
            $validateForm = $this->validateForm($form);
            $data = $validateForm->getData();

            FlatFeeDelivery::setConfigValue(FlatFeeDeliveryConfigValue::ENABLED, is_bool($data["enabled"]) ? (int) ($data["enabled"]) : $data["enabled"]);

            foreach ($data as $name => $value) {
                ConfigQuery::write("flatfeedelivery_" . $name, $value, false, true);
            }
            return $this->redirectToConfigurationPage();

        } catch (FormValidationException $e) {
            $error_message = $this->createStandardFormValidationErrorMessage($e);
        }

        if (null !== $error_message) {
            $this->setupFormErrorContext(
                'configuration',
                $error_message,
                $form
            );
            $response = $this->render("module-configure", ['module_code' => 'FlatFeeDelivery']);
        }
        return $response;
    }

    /**
     * Redirect to the configuration page
     */
    protected function redirectToConfigurationPage()
    {
        return RedirectResponse::create(URL::getInstance()->absoluteUrl('/admin/module/FlatFeeDelivery'));
    }
}