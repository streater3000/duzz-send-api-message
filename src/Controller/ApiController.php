<?php

namespace DuzzApi\Controller;

use Duzz\Core\Duzz_Get_Data;

class ApiController {
    const API_KEY = 'duzz_new_api_notification';
    private static $stored_message = '';
    const MESSAGE_TIMESTAMP_OPTION = 'duzz_message_timestamp';


    public function __construct() {
        // Fetch the message directly in the constructor
        self::$stored_message = Duzz_Get_Data::duzz_get_form_id('api_api_message_field_data', 'message');

        // Attach to the update option action to save a timestamp when the message changes
        add_action('update_option_api_api_message_field_data', [$this, 'on_message_option_updated'], 10, 2);
        
        // Register endpoint to provide the stored message
        add_action('rest_api_init', [$this, 'register_message_endpoint']);
    }

    public function on_message_option_updated($old_value, $new_value) {
        // Check if the message content has actually changed
        if ($old_value !== $new_value) {
            // Update the timestamp
            update_option(self::MESSAGE_TIMESTAMP_OPTION, time());
        }
    }

 public function register_message_endpoint() {
        register_rest_route('duzz_new/v1', '/updated_message/', [
            'methods' => 'GET',
            'callback' => [$this, 'provide_message'],
            'permission_callback' => '__return_true' // This is a public endpoint
        ]);
    }

    public function provide_message($request) {
        $apiKey = $request->get_param('api_key');
        if ($apiKey === self::API_KEY) {
            // Fetch the timestamp from the options
            $messageId = get_option(self::MESSAGE_TIMESTAMP_OPTION, time()); 
            return [
                'message_text' => self::$stored_message,
                'message_id' => $messageId
            ];
        }
        return new \WP_Error('wrong_api_key', 'Wrong API key provided', ['status' => 403]);
    }
}
