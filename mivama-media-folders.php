<?php
/**
 * Plugin Name: Mivama Media Folders
 * Plugin URI:  https://mivama.de/
 * Description: Adds real folder management to the native WordPress Media Library. Folders are stored safely as a hierarchical taxonomy on attachments, so file URLs never break.
 * Version:     1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author:      Mivama
 * Author URI:  https://mivama.de/
 * Text Domain: mivama-media-folders
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Mivama_Media_Folders
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MIVAMA_MEDIA_FOLDERS_FILE', __FILE__ );
define( 'MIVAMA_MEDIA_FOLDERS_DIR', plugin_dir_path( __FILE__ ) );
define( 'MIVAMA_MEDIA_FOLDERS_URL', plugin_dir_url( __FILE__ ) );

require_once MIVAMA_MEDIA_FOLDERS_DIR . 'includes/trait-mivama-media-folders-taxonomy.php';
require_once MIVAMA_MEDIA_FOLDERS_DIR . 'includes/trait-mivama-media-folders-assets.php';
require_once MIVAMA_MEDIA_FOLDERS_DIR . 'includes/trait-mivama-media-folders-admin-page.php';
require_once MIVAMA_MEDIA_FOLDERS_DIR . 'includes/trait-mivama-media-folders-ajax.php';
require_once MIVAMA_MEDIA_FOLDERS_DIR . 'includes/trait-mivama-media-folders-attachment-fields.php';
require_once MIVAMA_MEDIA_FOLDERS_DIR . 'includes/trait-mivama-media-folders-media-list.php';
require_once MIVAMA_MEDIA_FOLDERS_DIR . 'includes/trait-mivama-media-folders-queries.php';
require_once MIVAMA_MEDIA_FOLDERS_DIR . 'includes/trait-mivama-media-folders-helpers.php';
require_once MIVAMA_MEDIA_FOLDERS_DIR . 'includes/class-mivama-media-folders.php';

Mivama_Media_Folders::instance();

register_activation_hook(
	__FILE__,
	function () {
		$plugin = Mivama_Media_Folders::instance();
		$plugin->install_capabilities();
		$plugin->register_taxonomy();
		flush_rewrite_rules( false );
	}
);

register_deactivation_hook(
	__FILE__,
	function () {
		flush_rewrite_rules( false );
	}
);
