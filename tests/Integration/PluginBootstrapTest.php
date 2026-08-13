<?php

class Mivama_Media_Folders_Plugin_Bootstrap_Test extends WP_UnitTestCase
{
    public function test_plugin_bootstraps_and_exposes_singleton()
    {
        $this->assertTrue(class_exists('Mivama_Media_Folders'));
        $this->assertInstanceOf(Mivama_Media_Folders::class, Mivama_Media_Folders::instance());
        $this->assertSame('1.4.4', Mivama_Media_Folders::VERSION);
    }
}
