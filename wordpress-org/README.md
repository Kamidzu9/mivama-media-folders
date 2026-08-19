# WordPress.org Assets

This directory is repository-only release material for the WordPress.org plugin directory. It must never be included in the installable plugin ZIP.

## Expected assets

Prepare these before WordPress.org submission:

- `assets/icon-128x128.png`
- `assets/icon-256x256.png`
- `assets/banner-772x250.png`
- `assets/banner-1544x500.png`
- `assets/screenshot-1.png` — Media > Folders management screen
- `assets/screenshot-2.png` — Media Library folder filtering
- `assets/screenshot-3.png` — Attachment folder assignment
- `assets/screenshot-4.png` — Nested folder workflow

Keep screenshots representative of the exact `1.0.0` release candidate. Do not show development-only UI, test data containing personal information, or features that are not present in the released ZIP.

## Release rules

1. The installable artifact remains `dist/mivama-media-folders.zip` and must not contain this directory.
2. WordPress.org assets are published separately from the plugin code.
3. Use the same screenshots and copy that were approved during the `1.0.0` release sign-off.
4. After WordPress.org approval, SVN deployment automation should map this directory to the repository-level `/assets` directory while plugin code maps to `/trunk` and `/tags/<version>`.
