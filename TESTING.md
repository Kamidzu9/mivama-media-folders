# Release Testing

Use this checklist before publishing Mivama Media Folders `1.0.0`.

Automated CI is required but does not replace the wp-admin checks below. Test the exact release candidate from the generated ZIP, not a development checkout.

## 1. Release candidate preparation

- [ ] Confirm `mivama-media-folders.php` reports version `1.0.0`.
- [ ] Confirm `Mivama_Media_Folders::VERSION` is `1.0.0`.
- [ ] Confirm `readme.txt` uses `Stable tag: 1.0.0`.
- [ ] Confirm `composer version` passes.
- [ ] Confirm the PR has green `Quality gates`, `Release package`, `WordPress Plugin Check`, `Release ZIP smoke test`, all PHP lint jobs and all WordPress integration jobs.
- [ ] Build the candidate with `bash bin/build-release.sh`.
- [ ] Install only the generated plugin ZIP on the manual test site.

## 2. Clean install and activation

Test on a clean WordPress installation with no earlier Mivama Media Folders data.

- [ ] Upload the generated ZIP through Plugins > Add New > Upload Plugin.
- [ ] Activate the plugin without PHP errors, warnings or notices.
- [ ] Confirm `Media > Folders` appears for an administrator.
- [ ] Confirm the normal Media Library remains usable.
- [ ] Confirm activation does not move, rename or modify existing uploaded files.

## 3. Folder management

- [ ] Create a root folder.
- [ ] Create a nested child folder.
- [ ] Rename the root folder.
- [ ] Rename the child folder.
- [ ] Delete an empty folder.
- [ ] Delete a folder that previously contained assigned media and confirm attachments remain available.
- [ ] Confirm deleting a folder never deletes physical media files.

## 4. Attachment assignment

Use several images plus at least one non-image attachment.

- [ ] Assign an unassigned attachment to a folder.
- [ ] Reassign the attachment to another folder.
- [ ] Remove the attachment from its folder.
- [ ] Repeat with a non-image attachment.
- [ ] Confirm file URLs never change during assignment or reassignment.
- [ ] Confirm selecting an existing numeric folder ID does not create a folder named after that number.

## 5. Media Library list view

- [ ] Folder column renders correctly.
- [ ] Folder filter shows only matching attachments.
- [ ] Unassigned filter shows only unassigned attachments.
- [ ] Bulk move assigns selected attachments to the chosen folder.
- [ ] Bulk remove removes folder assignments without deleting attachments.
- [ ] Pagination and normal Media Library actions still work after filtering.

## 6. Media Library grid and media modals

- [ ] Grid-view folder filter works.
- [ ] Attachment details allow folder assignment and save correctly.
- [ ] Media modal opened from the editor works.
- [ ] Featured-image modal works.
- [ ] Gallery/media selection modal works.
- [ ] Folder UI does not block selecting, inserting or editing media.

## 7. Permissions

Create or use one account for each role.

### Administrator
- [ ] Can access `Media > Folders`.
- [ ] Can create, rename and delete folders.
- [ ] Can assign permitted attachments.

### Editor
- [ ] Expected folder-management access is available according to the `manage_media_folders` capability grant.
- [ ] Attachment operations still respect normal WordPress media permissions.

### Author
- [ ] Cannot gain structural folder-management privileges unless explicitly granted by WordPress capabilities.
- [ ] Cannot modify attachments that WordPress does not permit the author to edit.

### Subscriber
- [ ] Cannot manage folder structures.
- [ ] Cannot use protected folder-management AJAX/actions.

## 8. Security/error handling

- [ ] Invalid or expired nonces are rejected.
- [ ] Invalid attachment IDs are rejected safely.
- [ ] Invalid folder IDs are rejected safely.
- [ ] Unauthorized users cannot create, rename or delete folders.
- [ ] Unauthorized users cannot reassign attachments outside their normal WordPress permissions.
- [ ] Failed operations show a controlled error instead of a fatal error or broken admin page.

## 9. Deactivation and removal behavior

- [ ] Deactivate the plugin and confirm the Media Library still works.
- [ ] Reactivate it and confirm existing folder assignments return.
- [ ] Confirm deactivation does not delete attachments or physical files.
- [ ] Confirm the documented non-destructive data-retention behavior matches the observed result.

## 10. Final release sign-off

Only after every required item above passes:

- [ ] Merge the final release-readiness PR to `main`.
- [ ] Run **Actions > Release > Run workflow** with version `1.0.0`.
- [ ] Confirm tag `v1.0.0` is created by the workflow.
- [ ] Confirm the GitHub Release contains the installable ZIP and SHA-256 checksum.
- [ ] Verify the published ZIP checksum.
- [ ] Install the published ZIP once more on a fresh WordPress site.
- [ ] Perform a final activation + create-folder + assign-media smoke test.
- [ ] Only then announce or submit the release to WordPress.org.

## WordPress.org follow-up

Before directory submission:

- [ ] Validate `readme.txt` with the WordPress.org readme validator.
- [ ] Review all user-visible strings for translation readiness with the `mivama-media-folders` text domain.
- [ ] Prepare icon, banner and screenshots outside the plugin distribution ZIP.
- [ ] Confirm the submitted ZIP is byte-for-byte the intended `1.0.0` release artifact where the submission process permits it.
