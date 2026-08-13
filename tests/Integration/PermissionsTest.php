<?php

class Mivama_Media_Folders_Permissions_Test extends WP_UnitTestCase
{
    public function set_up()
    {
        parent::set_up();
        Mivama_Media_Folders::instance()->register_taxonomy();
    }

    public function test_subscriber_cannot_receive_attachment_folder_field()
    {
        $subscriber = self::factory()->user->create(array('role' => 'subscriber'));
        wp_set_current_user($subscriber);

        $attachment_id = self::factory()->post->create(array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
        ));

        $fields = Mivama_Media_Folders::instance()->add_attachment_folder_field(array(), get_post($attachment_id));
        $this->assertArrayNotHasKey(Mivama_Media_Folders::FIELD_KEY, $fields);
    }

    public function test_administrator_can_receive_attachment_folder_field()
    {
        $administrator = self::factory()->user->create(array('role' => 'administrator'));
        wp_set_current_user($administrator);

        $attachment_id = self::factory()->post->create(array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
        ));

        $fields = Mivama_Media_Folders::instance()->add_attachment_folder_field(array(), get_post($attachment_id));
        $this->assertArrayHasKey(Mivama_Media_Folders::FIELD_KEY, $fields);
    }
}
