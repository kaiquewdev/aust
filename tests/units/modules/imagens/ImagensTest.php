<?php
// require_once 'PHPUnit/Framework.php';

#####################################
require_once 'tests/config/auto_include.php';
require_once 'core/class/SQLObject.class.php';
require_once 'core/config/variables.php';
require_once 'core/libs/functions/func.php';
#####################################

class ImagensTest extends PHPUnit_Framework_TestCase
{
    public $lastSaveId;
	public $obj;
    
    public function setUp(){
		installModule('imagens');
        /*
         * MÓDULOS ATUAL
         *
         * Diretório do módulo
         */
        $this->mod = 'imagens';

        /*
         * Informações de conexão com banco de dados
         */

        include MODULES_DIR.$this->mod.'/'.MOD_CONFIG;
        include_once MODULES_DIR.$this->mod.'/'.$modInfo['className'].'.php';

        $this->obj = new $modInfo['className'];//new $modInfo['className']();
        $this->obj->testMode = true;
    }

    function testLoadSql(){
        $this->assertType('string', $this->obj->loadSql() );
        $this->assertType('string', $this->obj->loadSql(
                    array(
                        'limit' => 0,
                    ))
                );
        $this->assertEquals('imagens', $this->obj->useThisTable() );

        /*
         * Verifica SQL gerado, se é gerado corretamente
         */
        $sql = $this->obj->loadSql( array('') );
        $sql = preg_replace('/\n|\t/Us', "", preg_replace('/\s{2,}/s', " ", $sql));
        $this->assertEquals( trim("SELECT id, titulo, visitantes, systempath, categoria AS cat, ".
                        "DATE_FORMAT(".$this->obj->date['created_on'].", '".$this->obj->date['standardFormat']."') as adddate, ".
                        "(SELECT nome FROM categorias AS c WHERE id=cat ) AS node ".
                        "FROM imagens WHERE 1=1 ".
                        "ORDER BY id DESC ".
                        "LIMIT 0,25"),
                        trim($sql) );

        unset($sql);
        $sql = $this->obj->loadSql( array('page'=>3, 'id'=>'1') );
        $sql = preg_replace('/\n|\t/Us', "", preg_replace('/\s{2,}/s', " ", $sql));
        $this->assertEquals( trim("SELECT id, titulo, visitantes, systempath, categoria AS cat, ".
                        "DATE_FORMAT(".$this->obj->date['created_on'].", '".$this->obj->date['standardFormat']."') as adddate, ".
                        "(SELECT nome FROM categorias AS c WHERE id=cat ) AS node ".
                        "FROM imagens WHERE 1=1 AND id='1' ".
                        "ORDER BY id DESC ".
                        "LIMIT 50,25"),
                        trim($sql) );

        unset($sql);
        $sql = $this->obj->loadSql( array('page'=>3, 'id'=>'1', 'austNode' => array('3'=>'categoria1','4'=>'categoria1')) );
        $sql = preg_replace('/\n|\t/Us', "", preg_replace('/\s{2,}/s', " ", $sql));
        $this->assertEquals( trim("SELECT id, titulo, visitantes, systempath, categoria AS cat, ".
                        "DATE_FORMAT(".$this->obj->date['created_on'].", '".$this->obj->date['standardFormat']."') as adddate, ".
                        "(SELECT nome FROM categorias AS c WHERE id=cat ) AS node ".
                        "FROM imagens WHERE 1=1 AND id='1' AND categoria IN ('3','4') ".
                        "ORDER BY id DESC ".
                        "LIMIT 50,25"),
                        trim($sql) );
    }

	# no without image
    function testSaveDeleteLoad(){


        $_FILES = array(
            'frmarquivo' => array (
                'name' => 'test_file.gif',
                'type' => 'image/gif',
                'tmp_name' => dirname( __FILE__ ).'/test_file.gif',
                'error' => '0',
                'size' => '1409',
            )
        );
        $_POST = array(
            'method' => 'create',
            'frmadddate' => '2010-08-24',
            'frmautor' => '',
            'frmordem' => '777',
            'w' => '',
            'frmcategoria' => '777',
            'aust_node' => '777',
            'frmtitulo_encoded' => encodeText('777teste'),
            'frmtitulo' => '777teste',
            'frmlink' => '',
            'submit' => 'Enviar!',
        );
        
        $imagem = $this->obj->trataImagem($_FILES);
        $_POST["frmbytes"] = $imagem["filesize"];
        $_POST["frmnome"] = $imagem["filename"];
        $_POST["frmtipo"] = $imagem["filetype"];

        $saveResult = $this->obj->save($_POST);
        $lastInsertId = $this->obj->connection->lastInsertId();


        $loadResult = $this->obj->load(array( 'id' => $lastInsertId, 'austNode' => '777' ) );
        $deleteResult = $this->obj->delete($lastInsertId);

        $this->assertTrue($saveResult, $saveResult);
        $this->assertArrayHasKey('0', $loadResult, $saveResult);
        $this->assertTrue($deleteResult, $deleteResult);

        unset($_POST);
        unset($_FILES);


    }

	/*
	 * verifica se todas as configurações do arquivo config.php existem no método
	 * loadModConf()
	 */
	function testConfigurationsExists(){
		
        include MODULES_DIR.$this->mod.'/'.MOD_CONFIG;
		$configurations = $this->obj->loadModConf();
		foreach( $modInfo['configurations'] as $key=>$value ){
			$this->assertArrayHasKey($key, $configurations);
		}
	}


}
?>