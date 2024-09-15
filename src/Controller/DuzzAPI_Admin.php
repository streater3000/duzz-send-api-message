<?php

namespace DuzzApi\Controller;

use Duzz\Base\Admin\Factory\Duzz_Forms_Connector;

class DuzzAPI_Admin {

    private static $duzz_forms_api_connector;

    public static function init() {
        // Hooking the creation of connectors
        add_action('plugins_loaded', [self::class, 'create_duzz_forms_connectors']);
        
        // Adding filter to modify $field_data from DuzzApi_Menu_Items class
        add_filter('modify_duzz_field_data', [self::class, 'modify_field_data_callback']);
    }

    public static function create_duzz_forms_connectors() {
        if (!class_exists('\Duzz\Base\Admin\Factory\Duzz_Forms_Connector')) {
            return;
        }

        self::$duzz_forms_api_connector = new Duzz_Forms_Connector('api');
        
        // Use the static property here
        self::$duzz_forms_api_connector->duzz_add_section(self::settings_list_data('api_message'), 'api_message', 'textarea');    
        self::$duzz_forms_api_connector->duzz_init();
    }

    public static function duzz_api_message_field_connector_callback() {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        if (!self::$duzz_forms_api_connector) {
            return;
        }

        $option_group = self::$duzz_forms_api_connector->duzz_get_option_group();
        $page_slug = self::$duzz_forms_api_connector->duzz_get_page_slug();
        self::$duzz_forms_api_connector->duzz_output_form($option_group, $page_slug);
    }

    public static function settings_list_data($listType) {
        switch ($listType) {
            case 'api_message':
                return array(
                    'message' => 'Update:',
                );

            default:
                return array();
        }
    }

    public static function modify_field_data_callback($original_field_data) {
        // Use the static property here
        return array_merge($original_field_data, self::$available_selections);
    }
}

