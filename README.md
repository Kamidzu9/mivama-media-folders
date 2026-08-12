# Mivama Media Folders

A lightweight WordPress plugin that adds hierarchical folders to the native Media Library without physically moving uploaded files or changing their URLs.

## Features

- Create, edit and delete media folders
- Nested folder hierarchy
- Assign attachments to folders
- Folder filters in list and grid views
- Bulk move and remove-from-folder actions
- Unassigned media filter
- Native WordPress admin integration

## Requirements

- WordPress 6.0+
- PHP 7.4+

## Installation

1. Download or clone this repository.
2. Copy the plugin directory to `wp-content/plugins/mivama-media-folders` or install a release ZIP from WordPress Admin.
3. Activate **Mivama Media Folders**.
4. Open **Media → Folders**.

## How it works

Folders are stored as a private hierarchical taxonomy attached to media items. The plugin does **not** move physical files, so existing media URLs remain stable.

## Development

The repository contains the standalone plugin only. It was extracted from the former `mivama-digital/mivama-wordpress` repository so it can be maintained and released independently.

## License

GPL-2.0-or-later.
