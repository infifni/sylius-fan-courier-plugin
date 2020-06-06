<?php

/**
 * This file was created by the developers from Infifni.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://infifnisoftware.ro and write us
 * an email on contact@infifnisoftware.ro.
 */

declare(strict_types=1);

namespace Infifni\SyliusFanCourierPlugin\Form\Type;

use Infifni\FanCourierApiClient\Request\EndpointInterface;
use Infifni\FanCourierApiClient\Request\GenerateAwb;
use Infifni\SyliusFanCourierPlugin\Shipping\GatewayConfigProvider;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

final class ShippingGatewayType extends AbstractType
{
    public const FAN_GATEWAY_CODE = 'fan';
    public const BOOL_TRUE = true;
    public const BOOL_NO = false;
    private const TRANSLATION_KEY_MAP = [
        'standard' => 'standard',
        'collector_account' => 'collector_account',
        'red_code' => 'red_code',
        'specifications' => 'specifications',
        'express_loco_one_hour' => 'express_loco_one_hour',
        'express_loco_two_hours' => 'express_loco_two_hours',
        'express_loco_four_hours' => 'express_loco_four_hours',
        'express_loco_six_hours' => 'express_loco_six_hours',
        'express_loco_one_hour_collector_account' => 'express_loco_one_hour_collector_account',
        'express_loco_two_hours_collector_account' => 'express_loco_two_hours_collector_account',
        'express_loco_four_hours_collector_account' => 'express_loco_four_hours_collector_account',
        'express_loco_six_hours_collector_account' => 'express_loco_six_hours_collector_account',
        'red_code_collector_account' => 'red_code_collector_account',
        'white_goods' => 'white_goods',
        'white_goods_collector_account' => 'white_goods_collector_account',
        'freight_transport' => 'freight_transport',
        'freight_transport_collector_account' => 'freight_transport_collector_account',
        'white_goods_freight_transport' => 'white_goods_freight_transport',
        'white_goods_freight_transport_collector_account' => 'white_goods_freight_transport_collector_account',
    ];

    /**
     * @var AdapterInterface
     */
    private $cache;

    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws InvalidArgumentException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->cache->deleteItem(GatewayConfigProvider::GATEWAY_CACHE_KEY);

        $builder
            ->add('client_id', TextType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.fan_client_id.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.fan_client_id.field_help',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('username', TextType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.fan_username.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.fan_username.field_help',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('password', TextType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.fan_password.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.fan_password.field_help',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('parcels_or_envelopes', ChoiceType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.parcels_or_envelopes.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.parcels_or_envelopes.field_help',
                'choices' => [
                    'infifni.sylius_fan_courier_plugin.ui.parcels_or_envelopes.field_options.envelopes' => self::BOOL_NO,
                    'infifni.sylius_fan_courier_plugin.ui.parcels_or_envelopes.field_options.parcels' => self::BOOL_TRUE
                ]
            ])
            ->add('number_of_packages', IntegerType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.number_of_packages.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.number_of_packages.field_help',
                'required' => false
            ])
            ->add('who_pays_awb', ChoiceType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.who_pays_awb.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.who_pays_awb.field_help',
                'choices' => [
                    'infifni.sylius_fan_courier_plugin.ui.recipient' => GenerateAwb::RECIPIENT_ALLOWED_VALUE,
                    'infifni.sylius_fan_courier_plugin.ui.sender' => GenerateAwb::SENDER_ALLOWED_VALUE
                ]
            ])
            ->add('no_vat', ChoiceType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.no_vat.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.no_vat.field_help',
                'choices' => [
                    'infifni.sylius_fan_courier_plugin.ui.no' => self::BOOL_NO,
                    'infifni.sylius_fan_courier_plugin.ui.yes' => self::BOOL_TRUE
                ]
            ])
            ->add('hide_shipping_cost', ChoiceType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.hide_shipping_cost.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.hide_shipping_cost.field_help',
                'choices' => [
                    'infifni.sylius_fan_courier_plugin.ui.no' => self::BOOL_NO,
                    'infifni.sylius_fan_courier_plugin.ui.yes' => self::BOOL_TRUE
                ]
            ])
            ->add('free_shipping_min_value', TextType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.free_shipping_min_value.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.free_shipping_min_value.field_help',
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/(\d+(\.\d{2})?)/'
                    ])
                ]
            ])
            ->add('fixed_cost', TextType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.fixed_cost.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.fixed_cost.field_help',
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/(\d+(\.\d{2})?)/'
                    ])
                ]
            ])
            ->add('active_service', ChoiceType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.active_service.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.active_service.field_help',
                'choices' => $this->getAvailableServiceChoices()
            ])
            ->add('with_repayment', ChoiceType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.with_repayment.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.with_repayment.field_help',
                'choices' => [
                    'infifni.sylius_fan_courier_plugin.ui.no' => self::BOOL_NO,
                    'infifni.sylius_fan_courier_plugin.ui.yes' => self::BOOL_TRUE
                ]
            ])
            ->add('add_shipping_cost_to_repayment', ChoiceType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.add_shipping_cost_to_repayment.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.add_shipping_cost_to_repayment.field_help',
                'choices' => [
                    'infifni.sylius_fan_courier_plugin.ui.no' => self::BOOL_NO,
                    'infifni.sylius_fan_courier_plugin.ui.yes' => self::BOOL_TRUE
                ]
            ])
            ->add('who_pays_repayment', ChoiceType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.who_pays_repayment.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.who_pays_repayment.field_help',
                'choices' => [
                    'infifni.sylius_fan_courier_plugin.ui.recipient' => GenerateAwb::RECIPIENT_ALLOWED_VALUE,
                    'infifni.sylius_fan_courier_plugin.ui.sender' => GenerateAwb::SENDER_ALLOWED_VALUE
                ]
            ])
            ->add('with_assurance', ChoiceType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.with_assurance.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.with_assurance.field_help',
                'choices' => [
                    'infifni.sylius_fan_courier_plugin.ui.no' => self::BOOL_NO,
                    'infifni.sylius_fan_courier_plugin.ui.yes' => self::BOOL_TRUE
                ]
            ])
            ->add('with_product_codes_in_content', ChoiceType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.with_product_codes_in_content.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.with_product_codes_in_content.field_help',
                'choices' => [
                    'infifni.sylius_fan_courier_plugin.ui.no' => self::BOOL_NO,
                    'infifni.sylius_fan_courier_plugin.ui.yes' => self::BOOL_TRUE
                ]
            ])
            ->add('observations', TextType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.observations.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.observations.field_help',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 200
                    ])
                ]
            ])
            ->add('sender_contact_person', TextType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.sender_contact_person.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.sender_contact_person.field_help',
                'constraints' => [
                    new Length([
                        'max' => 30
                    ])
                ],
                'required' => false
            ])
            ->add('open_allowed', ChoiceType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.open_allowed.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.open_allowed.field_help',
                'choices' => [
                    'infifni.sylius_fan_courier_plugin.ui.no' => self::BOOL_NO,
                    'infifni.sylius_fan_courier_plugin.ui.yes' => self::BOOL_TRUE
                ]
            ])
            ->add('epod', ChoiceType::class, [
                'label' => 'infifni.sylius_fan_courier_plugin.ui.epod.field_name',
                'help' => 'infifni.sylius_fan_courier_plugin.ui.epod.field_help',
                'choices' => [
                    'infifni.sylius_fan_courier_plugin.ui.no' => self::BOOL_NO,
                    'infifni.sylius_fan_courier_plugin.ui.yes' => self::BOOL_TRUE
                ]
            ])
        ;
    }

    private function getAvailableServiceChoices(): array
    {
        $availableServices = EndpointInterface::SERVICE_ALLOWED_VALUES;
        unset($availableServices['export']);

        $baseTranslation = 'infifni.sylius_fan_courier_plugin.ui.active_service.field_options.';
        $choices = [];
        foreach ($availableServices as $availableServiceKey => $availableServiceValue) {
            $choices[$baseTranslation.self::TRANSLATION_KEY_MAP[$availableServiceKey]] = $availableServiceValue;
        }

        return $choices;
    }
}
