<?php
if (! defined('ABSPATH')) {
    exit;
}

trait Mivama_Media_Folders_Attachment_Fields
{
    public function add_attachment_folder_field($form_fields, $post)
    {
        if (! current_user_can('edit_post', $post->ID)) {
            return $form_fields;
        }

        $selected = $this->get_attachment_folder_id($post->ID);
        $options  = $this->render_folder_select_options($selected, __('No folder', 'mivama-media-folders'));

        $form_fields[self::FIELD_KEY] = array(
            'label' => __('Folder', 'mivama-media-folders'),
            'input' => 'html',
            'html'  => sprintf(
                '<div class="mivama-folder-control" data-attachment-id="%1$d"><select class="mivama-media-folder-select" name="attachments[%1$d][%2$s]" data-attachment-id="%1$d">%3$s</select><button type="button" class="button button-small mivama-save-folder-button" data-attachment-id="%1$d">%4$s</button><button type="button" class="button button-small mivama-new-folder-trigger">+ %5$s</button><p class="mivama-folder-status" aria-live="polite"></p><p class="help">%6$s</p></div>',
                absint($post->ID),
                esc_attr(self::FIELD_KEY),
                $options,
                esc_html__('Save folder', 'mivama-media-folders'),
                esc_html__('New folder', 'mivama-media-folders'),
                esc_html__('Choose an existing folder or create a new one. Changing the folder does not move the physical file.', 'mivama-media-folders')
            ),
        );

        return $form_fields;
    }

    public function save_attachment_folder_field($post, $attachment)
    {
        if (! isset($post['ID'], $attachment[self::FIELD_KEY])) {
            return $post;
        }

        $attachment_id = absint($post['ID']);
        if (! current_user_can('edit_post', $attachment_id)) {
            return $post;
        }

        $folder_id = absint($attachment[self::FIELD_KEY]);
        $this->assign_attachment_folder($attachment_id, $folder_id);

        return $post;
    }
}
