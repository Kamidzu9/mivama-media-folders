=== Mivama Media Folders ===
Contributors: mivama
Tags: media library, folders, attachments, admin
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.4.4
License: GPLv2 or later

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

== Important ==

This plugin does not physically move files on your server.
It assigns folder terms to attachments, which keeps all existing image/file URLs stable.

== Changelog ==

= 1.4.4 =
* Refactored the plugin into smaller include files.
* Loaded media folder controls across WordPress admin surfaces where the media modal can appear.

= 1.3.0 =
* Fixed numeric folder bug where selecting an existing folder could create a new folder named like the term ID, for example "9".
* Attachment field now uses a dedicated internal field key instead of the taxonomy name.
* Assignment now forces integer term_id usage before calling WordPress term assignment.
* Improved existing-folder validation by checking real term IDs.
* Bumped asset version to clear old admin JavaScript.

= 1.2.0 =
* Rebuilt Media > Folders page.
* Added folder edit/delete table.
* Added AJAX folder creation and attachment folder saving.
* Added folder filter and bulk move support.

= 1.1.0 =
* Added inline new-folder modal.

= 1.0.0 =
* Initial release.
