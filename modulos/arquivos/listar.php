<?php
$h1 = 'Listando arquivos: '.$aust->leNomeDaEstrutura($_GET[aust_node]);
$sql = "SELECT
			id,nome
		FROM
			$aust_table
		WHERE
			id='".$_GET['aust_node']."'";


$mysql = mysql_query($sql);
$dados = mysql_fetch_array($mysql);
$cat = $dados[nome];
?>
<p>
	<a href="adm_main.php?section=<?=$_GET['section'];?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>
<h1><?=$h1;?></h1>
<p>Abaixo você encontra a listagem dos últimos textos desta categoria.</p>
<?php
if((!empty($filter)) AND ($filter <> 'off')){
	$addurl = "&filter=$filter&filterw=" . urlencode($filterw);
}

$categorias = $aust->LeCategoriasFilhas('',$_GET['aust_node']);
$categorias[$_GET[aust_node]] = 'Estrutura';
//print_r($categorias);
$sql = $modulo->SQLParaListagem($categorias);
	//	echo '<br><br>'.$sql .'<br>';


$mysql = mysql_query($sql);

/*********************************
*
*	Começa a listagem
*
*********************************/
?>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>?section=<?=$_GET['section'];?>&action=actions&aust_node=<?=$_GET[aust_node];?>">
<a name="list">&nbsp;</a>
<div class="painel_de_controle"><input type="submit" name="deletar" value="Deletar selecionados" />
</div>
<table width="680" cellspacing="0" cellpadding="10" class="listagem">
    <tr class="titulo">

        <?php for($i=0; $i< count($content_header[campos]); $i++) { ?>
                <td class="<? echo $content_header[campos][$i]; ?>">
                    <?php
                        echo $content_header[campos_nome][$i];
                    ?>
                </td>
        <?php } ?>
        <td bgcolor="#333333" width="80" align="center">
            Op&ccedil;&otilde;es
        </td>
    </tr>
<?
if(mysql_num_rows($mysql) > 0){
    while($dados = mysql_fetch_array($mysql)){
    ?>
        <tr class="conteudo">
            <?php
            /*******************************
            *
            *
            *  LISTAGEM DO DB
            *
            *
            *******************************/
            for($i=0; $i< count($content_header[campos]); $i++) { ?>
                <td>
                    <?php
                    if($i == 1){
                        echo '<a href="adm_main.php?section='.$_GET['section'].'&action=editar&aust_node='.$_GET['aust_node'].'&w='.$dados["id"].'">';
                        echo $dados[$content_header[campos][$i]];
                        echo '</a>';
                    } else {
                        echo $dados[$content_header[campos][$i]];
                    }
                    ?>
                </td>
            <?php } ?>
            <td align="center">
                <input type='checkbox' name='itens[]' value='<?=$dados[id];?>'>
                <!-- <a href="adm_main.php?section=<?=$_GET['section']?>&action=see_info&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="img/layoutv1/lupa.jpg" alt="Ver Informações" border="0" /></a> -->
            <!--
                <a href="adm_main.php?section=<?=$_GET['section']?>&action=edit_form&aust_node=<?=$aust_node;?>&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="img/layoutv1/edit.jpg" alt="Editar" border="0" /></a>
                <?php
                if($escala == "administrador"
                OR $escala == "moderador"
                OR $escala == "webmaster"
                OR $_SESSION["loginid"] == $dados[autorid]){

                    if((!empty($filter)) AND ($filter <> 'off')){
                        $addurl = "&filter=$filter&filterw=" . urlencode($filterw);
                    }
                    ?>
                    <a href="adm_main.php?section=<?=$_GET['section']?>&action=<?=$action;?>&block=delete&aust_node=<?=$aust_node;?>&w=<?php echo $dados["id"]; ?><?php echo $addurl;?>" style="text-decoration: none;"><img src="img/layoutv1/delete.jpg" alt="Deletar" border="0" /></a>
                    <?php
                }
                ?>
                <?php
                // Verifica se tipo conteúdo atual está configurado para usar galeria de fotos
                if(in_array($cat, $aust_conf['where_gallery'])){ ?>
                    <a href="adm_main.php?section=<?=$_GET['section']?>&action=photo_content_manage&w=<?php echo $dados["id"]; ?>#add" style="text-decoration: none;"><img src="img/layoutv1/fotos.jpg" alt="Adicionar fotos a este conteúdo" border="0" /></a>
                <?php } ?>
               -->
            </td>
        </tr>
    <?php
    } // FIM DO WHILE
} else {
    ?>
    <tr>
        <td colspan="<?=count($content_header)+1;?>">
        <strong>Não há arquivos cadastrados.</strong>
        </td>
    </tr>
    <?
}
?>
</table>
</form>

<p style="margin-top: 15px;">
	<a href="adm_main.php?section=<?=$_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>