<?php
/**********************************

	GRAVAR
	
	Variáveis necessárias:
	$_POST -> contendo dados provenientes de formulário

**********************************/

$c = 0;

//pr($_FILES);

if( $_POST["metodo"] == "criar" AND !empty($_FILES) AND $_FILES["frmarquivo"]["size"] > 0 ){
    $save = true;
} else if( $_POST["metodo"] == "editar" ) {
    $save = true;
} else {
    $save = false;
}

if( !empty($_POST) AND $save  ) {
//if( false ) {

    /*
     * FILES
     *
     * Prepara campos relativos ao arquivo que foi feito upload
     */
    
    //pr($_FILES);
    if( !empty($_FILES) ){

        if( is_array( $_FILES["frmarquivo"]) ){
            foreach( $_FILES["frmarquivo"]["size"] as $chave=>$tamanho ){

                if( $tamanho > 0 ){

                    foreach( $_FILES["frmarquivo"] as $infoName=>$info ){
                        $arquivo[$infoName] = $_FILES["frmarquivo"][$infoName][$chave];
                    }
                    //if($arquivo["filesize"])
                    $imagem[] = $modulo->trataImagem($arquivo);
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
            if($_POST['metodo'] == 'criar') {
                if($c > 0) {
                    $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key);
                    $sqlvalorstr = $sqlvalorstr.",'".$valor."'";
                } else {
                    $sqlcampostr = str_replace('frm', '', $key);
                    $sqlvalorstr = "'".$valor."'";
                }
            } else if($_POST['metodo'] == 'editar') {
                if($c > 0) {
                    $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key).'=\''.$valor.'\'';
                } else {
                    $sqlcampostr = str_replace('frm', '', $key).'=\''.$valor.'\'';
                }
            }

            $c++;
        }
    }



    if($_POST['metodo'] == 'criar') {
        $sql = "INSERT INTO
                    ".$this->modulo->tabela_criar."
                    ($sqlcampostr)
                VALUES
                    ($sqlvalorstr)
            ";


        $h1 = 'Criando: '.$this->aust->leNomeDaEstrutura($_GET['aust_node']);
    } else if($_POST['metodo'] == 'editar') {
        $sql = "UPDATE
                ".$this->modulo->tabela_criar."
            SET
            $sqlcampostr
            WHERE
                id='".$_POST['w']."'
            ";
        $h1 = 'Editando: '.$this->aust->leNomeDaEstrutura($_GET['aust_node']);
    }

    $query = $this->modulo->conexao->exec($sql);
    //$query = false;

    /*
     * Salva dados
     */
    if( $query OR $_POST['metodo'] == 'editar' ) {
        $resultado = TRUE;

        // se estiver criando um registro, guarda seu id para ser usado por módulos embed a seguir
        if($_POST['metodo'] == 'criar') {
            $_POST['w'] = $this->modulo->conexao->conn->lastInsertId();
        }

        /*
         * Salvou dados sobre galeria, agora salva imagens novas
         */
        if( !empty($imagem) AND is_array($imagem) ){
            
            unset($erroImg);
            foreach( $imagem as $chave=>$valor ){
                $sqlImagem = "INSERT INTO galeria_fotos_imagens
                                (galeria_foto_id,
                                ordem,
                                bytes,
                                dados,
                                nome,
                                tipo,
                                adddate)
                                VALUES
                                (
                                    '".$_POST['w']."',
                                    IFNULL( ( SELECT MAX(g.ordem)+1 as gordem FROM galeria_fotos_imagens as g
                                      WHERE g.galeria_foto_id='".$_POST["w"]."'
                                      GROUP BY g.ordem ORDER BY gordem DESC LIMIT 1
                                    ), '1'),
                                    '".$valor["filesize"]."',
                                    '".addslashes($valor["filedata"])."',
                                    '".$valor["filename"]."',
                                    '".$valor["filetype"]."',
                                    NOW()
                                )
                                ";
                
                if( ! $this->modulo->conexao->exec($sqlImagem) )
                    $erroImg[] = $valor["filename"];
                    
                unset($sqlImagem);
            }

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
    <a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>