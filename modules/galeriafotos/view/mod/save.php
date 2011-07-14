<?php
/**********************************

	GRAVAR
	
	Variáveis necessárias:
	$_POST -> contendo dados provenientes de formulário

**********************************/

$c = 0;

//pr($_FILES);

if( $_POST["metodo"] == "create" AND !empty($_FILES) AND $_FILES["frmarquivo"]["size"] > 0 ){
    $save = true;
} else if( $_POST["metodo"] == "edit" ) {
    $save = true;
} else {
    $save = false;
}

if( !empty($_POST) AND $save  ) {
	
	// saving one comment to various images
	$comments = '';
	if( !empty($_POST['images_comment']) )
		$comments = addslashes( $_POST['images_comment'] );

    /*
     * FILES
     *
     * Prepara campos relativos ao arquivo que foi feito upload
     */
    
	$imageHandler = Image::getInstance();
//    pr($_FILES);
    if( !empty($_FILES) ){

        if( is_array( $_FILES["frmarquivo"]) ){
            foreach( $_FILES["frmarquivo"]["size"] as $chave=>$tamanho ){

                if( $tamanho > 0 ){

                    foreach( $_FILES["frmarquivo"] as $infoName=>$info ){
                        $arquivo[$infoName] = $_FILES["frmarquivo"][$infoName][$chave];
                    }
                    //if($arquivo["filesize"])
                    $imagem[] = $imageHandler->resample($arquivo);
                }
                unset($arquivo);
            }
        }

    }
    
    /*
     * Últimos ajustes de campos a serem inseridos
     */
    $_POST["frmcategoria"] = $_POST["aust_node"];
    $_POST['frmtitulo_encoded'] = encodeText($_POST['frmtitulo']);



    /*
     * Prepara os campos que serão usados para gerar o SQL de INSERT
     */

    foreach($_POST as $key=>$valor) {
    // se o argumento $_POST contém 'frm' no início
        if(strpos($key, 'frm') === 0) {
            $sqlcampo[] = str_replace('frm', '', $key);
            $sqlvalor[] = $valor;
            // ajusta os campos da tabela nos quais serão gravados dados
            $valor = addslashes($valor);
            if($_POST['metodo'] == 'create') {
                if($c > 0) {
                    $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key);
                    $sqlvalorstr = $sqlvalorstr.",'".$valor."'";
                } else {
                    $sqlcampostr = str_replace('frm', '', $key);
                    $sqlvalorstr = "'".$valor."'";
                }
            } else if($_POST['metodo'] == 'edit') {
                if($c > 0) {
                    $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key).'=\''.$valor.'\'';
                } else {
                    $sqlcampostr = str_replace('frm', '', $key).'=\''.$valor.'\'';
                }
            }

            $c++;
        }
    }



    if($_POST['metodo'] == 'create') {
        $sql = "INSERT INTO
                    ".$this->module->useThisTable()."
                    ($sqlcampostr)
                VALUES
                    ($sqlvalorstr)
            ";


        $h1 = 'Criando: '.Aust::getInstance()->leNomeDaEstrutura($_GET['aust_node']);
    } else if($_POST['metodo'] == 'edit') {
        $sql = "UPDATE
                    ".$this->module->useThisTable()."
                SET
                $sqlcampostr
                WHERE
                    id='".$_POST['w']."'
                ";
        $h1 = 'Editando: '.Aust::getInstance()->leNomeDaEstrutura($_GET['aust_node']);
    }

    $query = $this->module->connection->exec($sql);

    /*
     * Salva dados
     */
    if( $query OR $_POST['metodo'] == 'edit' ) {
        $resultado = TRUE;

        // se estiver criando um registro, guarda seu id para ser usado por módulos embed a seguir
        if($_POST['metodo'] == 'create') {
            $_POST['w'] = $this->module->connection->conn->lastInsertId();
        }

        /*
         * Salvou dados sobre galeria, agora salva imagens novas
         */
        if( !empty($imagem) AND is_array($imagem) ){
            
            unset($erroImg);
			$austNode = $_POST["aust_node"];
			$contentId = (empty($_POST["content_id"])) ? "" : $_POST["content_id"];

            foreach( $imagem as $chave=>$valor ){
				
				/*
				 * PADRÃO : Salva imagem fisicamente.
				 */
				if( !$this->module->getStructureConfig('save_into_db') ){
					
					$finalName = $imageHandler->upload($valor);
					
					$finalName['systemPath'] = addslashes($finalName['systemPath']);
					$finalName['webPath'] = addslashes($finalName['webPath']);

					$sqlBuffer[] = "(
										'".$austNode."',
				                        '".$_POST['w']."',
										'".$contentId."',
				                        IFNULL( ( SELECT MAX(g.ordem)+1 as gordem FROM galeria_fotos_imagens as g
				                          WHERE g.galeria_foto_id='".$_POST["w"]."'
				                          GROUP BY g.ordem ORDER BY gordem DESC LIMIT 1
				                        ), '1'),
				                        '".$valor["size"]."',
				                        '".$finalName['systemPath']."',
				                        '".$finalName['webPath']."',
				                        '".$valor["name"]."',
				                        '".$valor["type"]."',
				                        NOW(), '".$comments."'
				                    )";
					
				} else {
	
	                $sqlImagem = "INSERT INTO galeria_fotos_imagens
	                                (
									category_id
									galeria_foto_id,
	                                ordem,
	                                bytes,
	                                dados,
	                                nome,
	                                tipo,
	                                adddate)
	                                VALUES
	                                (
	                                    '".$_POST["aust_node"]."'
										'".$_POST['w']."',
	                                    IFNULL( ( SELECT MAX(g.ordem)+1 as gordem FROM galeria_fotos_imagens as g
	                                      WHERE g.galeria_foto_id='".$_POST["w"]."'
	                                      GROUP BY g.ordem ORDER BY gordem DESC LIMIT 1
	                                    ), '1'),
	                                    '".$valor["size"]."',
	                                    '".addslashes(file_get_contents($valor["tmp_name"]) )."',
	                                    '".$valor["name"]."',
	                                    '".$valor["type"]."',
	                                    NOW()
	                                )
	                                ";
                
	                if( ! $this->module->connection->exec($sqlImagem) )
	                    $erroImg[] = $valor["tmp_name"];
                    
	                unset($sqlImagem);
				}
            }

        }

		if( !empty($sqlBuffer) ){

			$sql = "INSERT INTO galeria_fotos_imagens
					(category_id, galeria_foto_id, content_id, ordem, bytes, systempath, path, nome, tipo, adddate, texto)
                    VALUES ".implode(",", $sqlBuffer);

            if( ! $this->module->connection->exec($sql) )
                $erroImg[] = 'várias imagens';
            
            unset($sqlBuffer);
		}

        if( !empty($erroImg) AND count($erroImg) > 0 ){
            echo "<p>As seguintes imagens não puderam ser salvas:</p>";
            echo "<ul>";
            foreach($erroImg as $valor){
                echo "<li>".$valor."</li>";
            }
            echo "</ul>";
            echo "<p>Esta falha pode ter ocorrido por defeito na imagem.</p>";
        }

    } else {
        $resultado = FALSE;
    }
	//	echo 'hey - '.count($imagem);
//	exit();

    if($resultado) {
        $status['classe'] = 'sucesso';
        $status['mensagem'] = '<strong>Sucesso: </strong> As informações foram salvas com sucesso.';
    } else {
        $status['classe'] = 'insucesso';
        $status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao salvar informações. Se você tentou copiar um texto do Microsoft Word, provavelmente há letras/caracteres neste texto que não podem ser lidos por seu navegador. Experimente verificar se não há nada de estranho (alguma letra) entre este texto. Se houver, entre em contato com o administrador e explique o que está acontecendo.';
    }
    EscreveBoxMensagem($status);

}
?>
<br />
<p>
    <a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>