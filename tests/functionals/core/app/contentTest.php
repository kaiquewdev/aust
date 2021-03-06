<?php
// require_once 'PHPUnit/Framework.php';
require_once 'tests/config/auto_include.php';

class ContentControllerTest extends PHPUnit_Framework_TestCase
{

    public function setUp(){
		require_once(CONTROLLERS_DIR."content_controller.php");
    }

	public function testIndex(){
        $this->obj = new ContentController;
		$this->assertRegExp('/<h2>Gerencie seu conteúdo<\/h2>/', $this->obj->render());
	}

	public function testConfigurationsWithModule(){
		
		Fixture::getInstance()->create();
		$query = Connection::getInstance()->query("SELECT id FROM taxonomy WHERE type='textual' AND class='estrutura' LIMIT 1");
		$this->assertArrayHasKey(0, $query);
		$structureId = $query[0]["id"];
		
		$_GET['aust_node'] = $structureId;
		$_GET['action'] = "listing";
        $this->obj = new ContentController(true);
		$this->assertEquals('load_structure', $this->obj->_action());
		
	}

}
?>