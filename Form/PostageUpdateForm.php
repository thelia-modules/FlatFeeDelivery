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

namespace FlatFeeDelivery\Form;

use FlatFeeDelivery\FlatFeeDelivery;
use FlatFeeDelivery\Model\Config\FlatFeeDeliveryConfigValue;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\Base\AreaDeliveryModuleQuery;
use Thelia\Model\ConfigQuery;
use Thelia\Model\ModuleQuery;

/**
 * Class PostageUpdateForm
 * @package FlatFeeDelivery\Form
 * @author Thomas Arnaud <tarnaud@openstudio.fr>
 */
class PostageUpdateForm extends BaseForm
{
    protected function buildForm()
    {
        $form = $this->formBuilder;

        $module_id = ModuleQuery::create()->findOneByFullNamespace('FlatFeeDelivery\FlatFeeDelivery')->getId();
        $areas = AreaDeliveryModuleQuery::create()->findByDeliveryModuleId($module_id);

        $this->formBuilder
            ->add("enabled",
                "checkbox",
                array(
                    "label" => $this->translator->trans("Enabled", [], FlatFeeDelivery::DOMAIN_NAME),
                    "label_attr" => [
                        "for" => "enabled",
                    ],
                    "value" => FlatFeeDelivery::getConfigValue(FlatFeeDeliveryConfigValue::ENABLED, false),
                )
            )
        ;

        foreach($areas as $area) {

            $form->add(
                'area_postage_' . $area->getAreaId(),
                'text',
                array(
                    "constraints" => [
                        new Callback(
                            [
                                "methods" => [
                                    [
                                        $this,
                                        "verifyValue"
                                    ]
                                ]
                            ]
                        )
                    ],
                    "data" => ConfigQuery::read("flatfeedelivery_" . 'area_postage_' . $area->getAreaId(), ""),
                    "label_attr" => array(
                        "for" => 'area_postage_' . $area->getAreaId()
                    ),
                )
            );
        }
    }

    /**
     * @param $str
     * @param array $params
     * @return string
     */
    protected function trans($str, $params = [])
    {
        return Translator::getInstance()->trans($str, $params, FlatFeeDelivery::DOMAIN_NAME);
    }

    /**
     * @param $value
     * @param ExecutionContextInterface $context
     */
    public function verifyValue($value, ExecutionContextInterface $context)
    {
        if (!preg_match("#^\d\.?\d*$#",$value)) {
            $context->addViolation($this->trans("enter a valid price"));
        }
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "flat_fee_delivery_form";
    }
}