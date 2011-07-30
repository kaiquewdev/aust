<?php
/*
 * CARREGA DADOS
 * Carrega dados das tabelas para amostragem do formulário de edição
 */

    $sql = "SELECT
                *
            FROM
                categorias
            WHERE
                id='".$_GET['w']."'
    ";
    $query = Connection::getInstance()->query($sql);
    $dados = $query[0];
?>


<span class="root_user_only">Apenas desenvolvedores acessam esta tela.</span>
<h2>Taxonomia: editar categoria</h2>
<p>Você pode editar a categoria <?php echo $dados['nome']?></p>

<form method="post" action="adm_main.php?section=<?php echo $_GET['section'];?>&action=update" enctype="multipart/form-data">
<input type="hidden" name="action" value="update">
<input type="hidden" name="autorid" value="<?php echo $loginid; ?>">
<input type="hidden" name="w" value="<?php echo $_GET['w']; ?>">
<table width="670" border=0 class="form">
<tr>
    <td><label>Categoria:</label> </td>
    <td>
        <strong><?php echo $dados['patriarca']?></strong>
    </td>
</tr>
<tr>
    <td valign="top"><label>Nome:</label> </td>
    <td>
        <input type="text" class="text" name="frmnome" value="<?php echo $dados['nome']?>">
        <p class="explanation">
            Digite o nome da categoria. (Começa com letra maiúscula e não leva
            ponto final)
        </p>
        <p class="explanation" id="exists_titulo">
        </p>
    </td>
</tr>
<tr>
    <td valign="top"><label>Descrição:</label> </td>
    <td>
        <textarea name="frmdescricao" rows="3" cols="40" id="jseditor"><?php echo $dados['descricao']?></textarea>
        <p class="explanation">
            Digite uma breve descrição desta categoria.
        </p>
    </td>
</tr>
<?php
/*
 * FOTO
 */

?>
<tr>
    <td valign="top" colspan="2">
        <?php
        /*
         * Mostra foto da categoria se houver
         */
        $sql = "SELECT id FROM austnode_images WHERE node_id='".$_GET['w']."' ORDER BY id DESC LIMIT 0,1";
        $query = Connection::getInstance()->query($sql);
        if(count($query)){
            $result = $query[0];
            ?>
            <div style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid silver;">
                <p><img src="<?php echo IMAGE_VIEWER_DIR;?>visualiza_foto.php?table=austnode_images&fromfile=true&myid=<?php echo $result['id']?>&thumbs=yes&&maxxsize=400&maxysize=400" /></p>
                <strong>Imagem atual: <?php echo $result['nome']?></strong>
            </div>
            <?php
        }
        ?>

        <p>
			Altere abaixo a imagem da categoria.
		</p>

    </td>
</tr>
<tr>
    <td valign="top"><label>Imagem:</label> </td>
    <td>
        <input type="file" name="arquivo" value="" />
    </td>
</tr>
<tr>
    <td colspan="2" style="padding-top: 30px;"><center><input type="submit" value="Enviar"></center></td>
</tr>
</table>

</form>
<p>
    <a href="adm_main.php?section=taxonomy&action=list_content">Voltar</a>
</p>