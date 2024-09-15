<?php

namespace DuzzApi\Controller;

class DuzzAPI_Menu {

    public function register_menu() {  
        add_menu_page(
            'API Page Title',
            'API Menu',
            'activate_plugins',
            'duzzapi_menu_slug',
            array(DuzzAPI_Admin::class, "duzz_api_message_field_connector_callback"),
            'dashicons-admin-generic',
            3
        );

        // Add submenu for editing changelog
        add_submenu_page(
            'duzzapi_menu_slug',
            'Edit Changelog',
            'Edit Changelog',
            'activate_plugins',
            'duzzapi_edit_changelog',
            array($this, 'edit_changelog_callback')
        );
    }

    public function setup() {
        add_action('admin_menu', array($this, 'register_menu'));
        add_action('admin_post_update_complete_changelog', array($this, 'handle_form_submission'));  // Handle the form submission
    }

    public function edit_changelog_callback() {
        $changelog = get_option('duzz_complete_changelog', '');

        echo '<div class="wrap">';
        echo '<h2>Edit Changelog</h2>';
        echo '<form method="post" action="' . admin_url('admin-post.php') . '">';

        echo "<textarea name='complete_changelog' rows='30' cols='100'>" . esc_textarea($changelog) . "</textarea>";
        echo '<br><br>';

        echo '<input type="hidden" name="action" value="update_complete_changelog">';
        echo '<input type="submit" value="Update Changelog" class="button button-primary">';
        echo '</form>';
        echo '</div>';
    }

public function handle_form_submission() {
    if (isset($_POST['complete_changelog'])) {
        $sanitized_changelog = wp_kses_post($_POST['complete_changelog']);
        update_option('duzz_complete_changelog', $sanitized_changelog);
    }

    // Redirect back to the edit page with a success message
    wp_redirect(admin_url('admin.php?page=duzzapi_edit_changelog&update=success'));
    exit;
}

}
