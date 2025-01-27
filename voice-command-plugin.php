<?php
/*
Plugin Name: Voice Command Plugin
Plugin URI: https://github.com/rafiqcoder/Voice-Command-Navigation.git
Description: Add a voice command button widget for Elementor.
Version: 1.2.2
Author: Your Name
Author URI: https://yourwebsite.com
License: GPL2
GitHub Plugin URI: https://github.com/rafiqcoder/Voice-Command-Navigation.git
GitHub Branch: main
*/



if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
define('VOICE_COMMAND_PLUGIN_VERSION', '1.2');

// Add a menu item in the WordPress admin
function vcp_add_admin_menu() {
    add_menu_page(
        'Voice Command Links',
        'Voice Commands',
        'manage_options',
        'voice-command-links',
        'vcp_render_admin_page',
        'dashicons-microphone',
        80
    );
}
add_action('admin_menu', 'vcp_add_admin_menu');

// Render the admin page
function vcp_render_admin_page() {
    // Get the existing links
    $vcp_links = get_option('vcp_links', []);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['vcp_links']) && is_array($_POST['vcp_links'])) {
            // Sanitize and save the links
            $sanitized_links = array_map(function ($link) {
                return [
                    'command' => sanitize_text_field($link['command']),
                    'url' => esc_url_raw($link['url']),
                ];
            }, $_POST['vcp_links']);

            update_option('vcp_links', $sanitized_links);
            $vcp_links = $sanitized_links;

            echo '<div class="updated"><p>Links updated successfully.</p></div>';
        }
    }
    ?>
    <div class="wrap">
        <h1>Manage Voice Command Links</h1>
        <form method="post">
            <table class="form-table" id="vcp-links-table">
                <thead>
                    <tr>
                        <th>Command</th>
                        <th>URL</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($vcp_links)) : ?>
                        <?php foreach ($vcp_links as $index => $link) : ?>
                            <tr>
                                <td>
                                    <input type="text" name="vcp_links[<?php echo $index; ?>][command]" value="<?php echo esc_attr($link['command']); ?>" required>
                                </td>
                                <td>
                                    <input type="url" name="vcp_links[<?php echo $index; ?>][url]" value="<?php echo esc_url($link['url']); ?>" required>
                                </td>
                                <td>
                                    <button type="button" class="button vcp-remove-row">Remove</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <button type="button" class="button button-primary" id="vcp-add-row">Add Link</button>
            <br><br>
            <input type="submit" class="button button-primary" value="Save Links">
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableBody = document.querySelector('#vcp-links-table tbody');
            const addRowBtn = document.getElementById('vcp-add-row');

            addRowBtn.addEventListener('click', function () {
                const rowCount = tableBody.rows.length;
                const newRow = `
                    <tr>
                        <td><input type="text" name="vcp_links[${rowCount}][command]" required></td>
                        <td><input type="url" name="vcp_links[${rowCount}][url]" required></td>
                        <td><button type="button" class="button vcp-remove-row">Remove</button></td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('beforeend', newRow);
            });

            tableBody.addEventListener('click', function (event) {
                if (event.target.classList.contains('vcp-remove-row')) {
                    const row = event.target.closest('tr');
                    row.remove();
                }
            });
        });
    </script>
    <?php
}

// function vcp_add_voice_command_button() {
//     echo '<button id="voice-command-btn" style="position: fixed; top: 10px; right: 10px; z-index: 1000; padding: 10px 15px; background-color: #0073aa; color: #fff; border: none; border-radius: 5px; cursor: pointer;">Start Voice Command</button>';
// }
// add_action('wp_footer', 'vcp_add_voice_command_button');


function vcp_enqueue_scripts() {
    wp_enqueue_script(
        'voice-command-js',
        plugin_dir_url(__FILE__) . 'assets/voice-command.js',
        [],
        '1.2',
        true
    );
	 wp_enqueue_script(
        'voice-command-css',
        plugin_dir_url(__FILE__) . 'assets/voice-command.css',
        [],
        '1.2',
        true
    );

    $links = get_option('vcp_command_links', []);
    wp_localize_script('voice-command-js', 'vcpLinks', $links);
}
add_action('wp_enqueue_scripts', 'vcp_enqueue_scripts');


// Pass dynamic links to the front-end
function vcp_add_dynamic_links() {
    $vcp_links = get_option('vcp_links', []);
    echo '<script>const vcpLinks = ' . json_encode($vcp_links) . ';</script>';
}
add_action('wp_footer', 'vcp_add_dynamic_links');
function register_voice_command_widget($widgets_manager) {
    require_once(__DIR__ . '/voice-command-widget.php');
    $widgets_manager->register(new \Voice_Command_Widget());
}
add_action('elementor/widgets/register', 'register_voice_command_widget');


function vcp_check_for_github_updates($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    $plugin_slug = 'voice-command-plugin';
    $github_api_url = 'https://github.com/rafiqcoder/Voice-Command-Navigation/releases/latest';

    $response = wp_remote_get($github_api_url);

    if (is_wp_error($response)) {
        return $transient;
    }

    $release_info = json_decode(wp_remote_retrieve_body($response), true);
    if (!isset($release_info['tag_name'])) {
        return $transient;
    }

    $latest_version = ltrim($release_info['tag_name'], 'v');
    $current_version = VOICE_COMMAND_PLUGIN_VERSION;

    if (version_compare($current_version, $latest_version, '<')) {
        $transient->response["{$plugin_slug}/{$plugin_slug}.php"] = (object) [
            'slug'        => $plugin_slug,
            'plugin'      => "{$plugin_slug}/{$plugin_slug}.php",
            'new_version' => $latest_version,
            'url'         => $release_info['html_url'],
            'package'     => $release_info['assets'][0]['browser_download_url'],
        ];
    }

    return $transient;
}
add_filter('site_transient_update_plugins', 'vcp_check_for_github_updates');
