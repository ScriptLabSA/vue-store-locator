<?php
/**
 * VslConstants
 */
defined('ABSPATH') || exit();


class VslConstants{

    const FIELDS = array(
        array(
            'name' => 'store_email',
            'label' => 'Email',
            'value' => '',
            'type' => 'email'
        ),
        array(
            'name' => 'store_phone',
            'label' => 'Phone',
            'value' => '',
            'type' => 'tel'
        ),
        array(
            'name' => 'store_street_address',
            'label' => 'Street Address',
            'value' => '',
            'type' => 'text'
        ),
        array(
            'name' => 'store_suburb',
            'label' => 'Suburb',
            'value' => '',
            'type' => 'text'
        ),
        array(
            'name' => 'store_city',
            'label' => 'City',
            'value' => '',
            'type' => 'text'
        ),
        array(
            'name' => 'store_province',
            'label' => 'Province',
            'value' => '',
            'type' => 'text'
        ),
        array(
            'name' => 'store_country',
            'label' => 'Country',
            'value' => '',
            'type' => 'text'
        )
    );
}