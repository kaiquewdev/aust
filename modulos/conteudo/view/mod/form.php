<?php

/*
 * FORMULÁRIO
 */

/*
 * Carrega configurações automáticas do DB
 */

    $params = array(
        "aust_node" => $_GET["aust_node"],
    );

    $moduloConfig = $modulo->loadModConf($params);


/*
 * Ajusta variáveis iniciais
 */
    $aust_node = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';
    $w = (!empty($_GET['w'])) ? $_GET['w'] : '';

/*
 * [Se novo conteúdo]
 */
    if($_GET['action'] == 'criar'){
        $tagh1 = "Criar: ". $this->aust->leNomeDaEstrutura($_GET['aust_node']);
        $tagp = 'Crie um novo conteúdo abaixo.';
        $dados = array('id' => '');
    }
/*
 * [Se modo edição]
 */
    else if($_GET['action'] == 'editar'){

        $tagh1 = "Editar: ". $this->aust->leNomeDaEstrutura($_GET['aust_node']);
        $tagp = 'Edite o conteúdo abaixo.';
        $sql = "
                SELECT
                    *
                FROM
                    ".$modulo->tabela_criar."
                WHERE
                    id='$w'
                ";
        $query = $modulo->conexao->query($sql);
        $dados = $query[0];
    }
?>
<p>
    <a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>

<h1><?php echo $tagh1;?></h1>
<p><?php echo $tagp;?></p>



<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET["section"] ?>&action=save" enctype="multipart/form-data" >
<input type="hidden" name="metodo" value="<?php echo $_GET['action'];?>">
<?php if($_GET['action'] == 'criar'){ ?>
    <input type="hidden" name="frmadddate" value="<?php echo date("Y-m-d H:i:s"); ?>">
    <input type="hidden" name="frmautor" value="<?php echo $_SESSION['loginid'];?>">
<?php } else { ?>

    <input type="hidden" name="frmadddate" value="<?php ifisset( $dados['adddate'] );?>">
    <input type="hidden" name="frmautor" value="<?php ifisset( $dados['autor'] );?>">

<?php }?>
<input type="hidden" name="w" value="<?php ifisset( $dados['id'] );?>">
<input type="hidden" name="aust_node" value="<?php echo $austNode; ?>">
<table width="670" border=0 cellpadding=0 cellspacing=0>
    <col width="200">
    <col width="470">
    <tr>
        <td valign="top"><label>Categoria:</label></td>
        <td>
            <div id="categoriacontainer">
            <?php
            $current_node = '';
            if($_GET['action'] == "editar"){
                $current_node = $dados['categoria'];
                ?>
                <input type="hidden" name="frmcategoria" value="<?php echo $current_node; ?>">
                <?php
            }

            echo BuildDDList( CoreConfig::read('austTable') ,'frmcategoria', $administrador->tipo ,$aust_node, $current_node);
            ?>
            </div>

        </td>
    </tr>
    <tr>
        <td valign="top"><label>Título:</label></td>
        <td>
            <INPUT TYPE='text' NAME='frmtitulo' class='text' value='<?php if( !empty($dados['titulo']) ) echo $dados['titulo'];?>' />
            <p class="explanation">

            </p>
        </td>
    </tr>

    <?php
    /*
     * RESUMO
     */
    $showResumo = true;
    if( !empty($moduloConfig["resumo"]) ){
        if( $moduloConfig["resumo"]["valor"] == "0" )
            $showResumo = false;
    }
    if( $showResumo ){
    ?>
    <tr>
        <td valign="top"><label>Resumo:</label></td>
        <td>
            <INPUT TYPE='text' NAME='frmresumo' class='text' value='<?php if( !empty($dados['resumo']) ) echo $dados['resumo'];?>' />
            <p class="explanation">

            </p>
        </td>
    </tr>
    <?php
    }
    ?>

    <?php
    /*
     * ORDEM
     */
    $showOrdem = false; // por padrão, não mostra
    if( !empty($moduloConfig["ordenate"]) ){
        if( $moduloConfig["ordenate"]["valor"] == "1" )
            $showOrdem = true;
    }
    if( $showOrdem ){
    ?>
    <tr>
        <td valign="top"><label>Ordem:</label></td>
        <td>
            <select name="frmordem" class="select">
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '10'); ?> value="10">10</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '9'); ?> value="9">9</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '8'); ?> value="8">8</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '7'); ?> value="7">7</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '6'); ?> value="6">6</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '5'); ?> value="5">5</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '4'); ?> value="4">4</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '3'); ?> value="3">3</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '2'); ?> value="2">2</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '1'); ?> value="1">1</option>
            </select>
            <p class="explanation">
                Selecione um número que representa a importância deste item.
                Quanto maior o número, maior a prioridade.
            </p>
        </td>
    </tr>
    <?php
    }
    ?>
    <tr>
        <td colspan="2"><label>Texto: </label>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <textarea name="frmtexto" id="jseditor" rows="20" style="width: 670px"><?php if( !empty($dados['texto']) ) echo $dados['texto'];?></textarea>
        <br />
        </td>
    </tr>
    <tr>
        <td valign="top"><label>Modo:</label></td>
        <td>
            <select name="frmrestrito" class="select">
                <option <?php if( !empty($dados['restrito']) ) makeselected($dados['restrito'], 'normal'); ?> value="normal">Mostrar em todas as páginas</option>
                <option <?php if( !empty($dados['restrito']) ) makeselected($dados['restrito'], 'naofrontend'); ?> value="naofrontend">Não mostrar na página principal</option>
                <option <?php if( !empty($dados['restrito']) ) makeselected($dados['restrito'], 'invisivel'); ?> value="invisivel">Tornar invisível este item em todo o site</option>
            </select>
            <p class="explanation">
                Selecione acima que tipo de exibição você deseja para este conteúdo.
            </p>
        </td>
    </tr>

    <?php
    /*
     * EMBED
     * mostra <input> de módulos embed
     *
     * Embed significa que os <input>s aqui mostrados serão enviados juntamente
     * com o <form> principal
     *
     * O arquivo inserido é /embed/form.php do módulo que $embed==true
     */

        //include(INC_DIR.'conteudo.inc/form_embed.php');

    ?>
    <tr>
        <td colspan="2" style="padding-top: 10px;"><center><INPUT TYPE="submit" VALUE="Enviar!" name="submit" class="submit"></center></td>
    </tr>
</table>

</form>


<?php
    /*
     * EMBED OWN FORM
     * mostra <input> de módulos embedownform
     *
     * Embed Own Form significa que o formulário possui a própria tag <form>, não
     * dependendo do <form> principal
     *
     * É padrão e pode ser copiado para todos os forms
     */

        include(INC_DIR.'conteudo.inc/form_embedownform.php');
?>


<br />
<p>
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>