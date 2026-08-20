=== Mivama Media Folders ===
Contributors: mivama
Tags: media library, folders, attachments, admin
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds folder management to the native WordPress Media Library without moving files or changing media URLs.

== Description ==

Mivama Media Folders adds a safe folder layer to the default WordPress Media Library.
Folders are stored as a private hierarchical taxonomy assigned to attachments.
That means files stay exactly where WordPress uploaded them, and existing URLs do not break.

Features:

* Media > Folders admin page
* Create, edit and delete media folders
* Nested folder support
* Assign existing media files to folders from attachment details
* AJAX save in the Media Library grid/list details panel
* Folder filter in the Media Library list view
* Folder filter in the Media Library grid view
* Bulk action: Move to selected folder
* Bulk action: Remove from folder
* Unassigned filter

== Installation ==

1. Upload the plugin ZIP in Plugins > Add New > Upload Plugin.
2. Activate the plugin.
3. Go to Media > Folders to create folders.
4. Open Media Library and assign files to folders from attachment details or list bulk actions.

== Frequently Asked Questions ==

= Does Mivama Media Folders move uploaded files? =

No. Folders are stored as taxonomy terms assigned to attachments. Physical files stay in their existing WordPress uploads directory.

= Will existing media URLs change? =

No. Assigning, moving or removing a media item from a folder does not change its file path or URL.

= Can folders be nested? =

Yes. Media folders are hierarchical and can contain child folders.

= What happens to media when I delete a folder? =

Deleting a media folder does not delete the attachments or physical files. The affected media remains available in the WordPress Media Library.

= What happens when I deactivate or remove the plugin? =

The plugin does not delete attachments or physical media files. Folder taxonomy data is intentionally not purged by an uninstall routine, so removing the plugin does not perform destructive media cleanup.

= Who can manage folder structures? =

Folder creation, editing and deletion use the dedicated `manage_media_folders` capability. The plugin grants it to trusted roles that can manage WordPress categories. Assigning attachments continues to respect normal WordPress media and per-attachment permissions.

== Important ==

This plugin does not physically move files on your server.
It assigns folder terms to attachments, which keeps all existing image/file URLs stable.

== Changelog ==

= 1.0.0 =
* Initial public release.
* Added native Media Library folder management with nested folders, filtering and bulk actions.
* Added safe taxonomy-based organization without moving files or changing media URLs.
* Added dedicated folder-management permissions and authorization/nonce regression coverage.
* Added reproducible CI, PHP compatibility checks and WordPress integration tests.
* Added WordPress Plugin Check, release ZIP validation and clean install/activation smoke testing.
* Added verified release automation with synchronized metadata and SHA-256 checksums.
* Documented non-destructive uninstall behavior and release/rollback procedures.
