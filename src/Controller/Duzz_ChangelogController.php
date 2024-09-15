<?php

namespace DuzzApi\Controller;

use WP_REST_Request;
use WP_REST_Response;

class Duzz_ChangelogController {

    const SECRET_TOKEN = 'aX3fH78lmP1Q088923Uv90Z5OpVwXY6C8823';

    public function __construct() {
        add_action('rest_api_init', [ $this, 'register_changelog_endpoint' ]);
        add_shortcode('duzz_display_changelog', [ $this, 'display_changelog' ]);
    }

    public function register_changelog_endpoint() {
        register_rest_route('duzz/v1', '/update_changelog/', [
            'methods' => 'POST',
            'callback' => [ $this, 'update_changelog_callback' ],
            'permission_callback' => '__return_true' 
        ]);
    }

public function update_changelog_callback( WP_REST_Request $request ) {
    $version = $request->get_param('version');
    $changelog = $request->get_param('changelog');
    
    if (!$version) {
        return new WP_REST_Response([ 'status' => 'error', 'message' => 'Version is required.' ], 400);
    }

    $date = date('Y-m-d');

    // Convert the changelog to Gutenberg block style
    $changelog_items = explode("\n", $changelog);
    $formatted_items = [];
    foreach ($changelog_items as $item) {
        if (strpos($item, "* ") === 0) {
            $item = substr($item, 2);
            $formatted_items[] = "<li>" . $item . "</li>";
        }
    }
    $changelog_block = implode("\n", $formatted_items);

    // New changelog entry
    $new_changelog = "\n= $version - $date =\n<ul>\n" . $changelog_block . "\n</ul>\n";

    $post_content = get_post_field('post_content', 9952230609011822);

    $position = strpos($post_content, "== Changelog ==");

    if ($position !== false) {
        $position += strlen("== Changelog ==");  // Move the position right after "== Changelog =="

        // Check if the content immediately after is a </p>
        if (substr($post_content, $position, 4) == "</p>") {
            $position += 4;  // Move the position right after "</p>"
            $new_changelog = "\n" . $new_changelog; // Add an extra newline before the new changelog
        }

        $post_content = substr_replace($post_content, $new_changelog, $position, 0);
    } else {
        $post_content = $new_changelog . $post_content;
    }

    wp_update_post([
        'ID' => 9952230609011822,
        'post_content' => $post_content
    ]);

    return new WP_REST_Response([ 'status' => 'success' ], 200);
}


    public function update_changelog_permission_callback( $request ) {
        $token = $request->get_header('Authorization');
        return $token === self::SECRET_TOKEN;
    }

public function display_changelog() {
    $changelog = get_post_field('post_content', 9952230609011822);
    if ($changelog) {
        // Adding an extra newline for visual separation
        return "== Changelog ==\n\n" . nl2br($changelog);
    } else {
        return 'No changelog available.';
    }
}


}
