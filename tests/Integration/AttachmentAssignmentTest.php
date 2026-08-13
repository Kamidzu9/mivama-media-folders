<?php

class Mivama_Media_Folders_Attachment_Assignment_Test extends WP_UnitTestCase
{
    private $user_id;

    public function set_up()
    {
        parent::set_up();
        Mivama_Media_Folders::instance()->register_taxonomy();
        $this->user_id = self::factory()->user->create(array('role' => 'administrator'));
        wp_set_current_user($this->user_id);
    }

    public function test_attachment_can_be_assigned_and_removed_from_folder()
    {
        $attachment_id = self::factory()->post->create(array('post_type' => 'attachment', 'post_status' => 'inherit'));
        $folder = wp_insert_term('Products', Mivama_Media_Folders::TAXONOMY);
        $this->assertNotWPError($folder);

        apply_filters(
            'attachment_fields_to_save',
            array('ID' => $attachment_id),
            array(Mivama_Media_Folders::FIELD_KEY => (string) $folder['term_id'])
        );

        $terms = wp_get_object_terms($attachment_id, Mivama_Media_Folders::TAXONOMY, array('fields' => 'ids'));
        $this->assertSame(array((int) $folder['term_id']), array_map('intval', $terms));
        $this->assertNull(term_exists((string) $folder['term_id'], Mivama_Media_Folders::TAXONOMY));

        apply_filters(
            'attachment_fields_to_save',
            array('ID' => $attachment_id),
            array(Mivama_Media_Folders::FIELD_KEY => '0')
        );

        $terms = wp_get_object_terms($attachment_id, Mivama_Media_Folders::TAXONOMY, array('fields' => 'ids'));
        $this->assertSame(array(), $terms);
    }
}
