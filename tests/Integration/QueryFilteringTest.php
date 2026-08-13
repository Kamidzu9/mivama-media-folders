<?php

class Mivama_Media_Folders_Query_Filtering_Test extends WP_UnitTestCase
{
    public function set_up()
    {
        parent::set_up();
        Mivama_Media_Folders::instance()->register_taxonomy();
        wp_set_current_user(self::factory()->user->create(array('role' => 'administrator')));
    }

    public function test_grid_query_filters_by_folder_and_includes_children()
    {
        $parent = wp_insert_term('Marketing', Mivama_Media_Folders::TAXONOMY);
        $child = wp_insert_term('Social', Mivama_Media_Folders::TAXONOMY, array(
            'parent' => (int) $parent['term_id'],
        ));

        $query = Mivama_Media_Folders::instance()->filter_media_grid_query(array(
            Mivama_Media_Folders::TAXONOMY => (string) $parent['term_id'],
        ));

        $this->assertArrayHasKey('tax_query', $query);
        $clause = end($query['tax_query']);
        $this->assertSame(Mivama_Media_Folders::TAXONOMY, $clause['taxonomy']);
        $this->assertSame(array((int) $parent['term_id']), array_map('intval', $clause['terms']));
        $this->assertTrue($clause['include_children']);
        $this->assertNotWPError($child);
    }

    public function test_grid_query_can_filter_unassigned_media()
    {
        $query = Mivama_Media_Folders::instance()->filter_media_grid_query(array(
            Mivama_Media_Folders::TAXONOMY => '-1',
        ));

        $this->assertArrayHasKey('tax_query', $query);
        $clause = end($query['tax_query']);
        $this->assertSame('NOT EXISTS', $clause['operator']);
        $this->assertSame(Mivama_Media_Folders::TAXONOMY, $clause['taxonomy']);
    }
}
