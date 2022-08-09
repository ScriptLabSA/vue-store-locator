<?php
/**
 * VslConstants
 */
defined( 'ABSPATH' ) || exit();

class VslConstants {

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

    const SETTINGS_TYPE = array(
        array(
            'label' => 'Text',
            'key' => 'text'
        ),
        array(
            'label' => 'URL',
            'key' => 'url'
        ),
        array(
            'label' => 'Number',
            'key' => 'number'
        ),
        array(
            'label' => 'Email',
            'key' => 'email'
        ),
        array(
            'label' => 'Select',
            'key' => 'select'
        )
    );

    const DEFAULT_SETTINGS = array(
        array(
            'name' => 'store_email',
            'label' => 'Email',
            'type' => 'email'
        ),
        array(
            'name' => 'store_phone',
            'label' => 'Phone',
            'type' => 'text'
        ),
        array(
            'name' => 'store_street_address',
            'label' => 'Street Address',
            'type' => 'text'
        ),
        array(
            'name' => 'store_suburb',
            'label' => 'Suburb',
            'type' => 'text'
        ),
        array(
            'name' => 'store_city',
            'label' => 'City',
            'type' => 'text'
        ),
        array(
            'name' => 'store_province',
            'label' => 'Province',
            'type' => 'text'
        ),
        array(
            'name' => 'store_country',
            'label' => 'Country',
            'type' => 'text'
        )
    );
}