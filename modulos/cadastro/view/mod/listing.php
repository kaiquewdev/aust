<?php
/*
 * LISTAR USUÁRIOS CADASTRADOS
 *
 * Este arquivo contém tudo necessário para listagem geral de usuários cadastrados
 */

// configuração: ajusta variáveis
$tabela = $modulo->LeTabelaDeDados($_GET['aust_node']);
$precisa_aprovacao = $modulo->pegaConfig(Array('estrutura'=>$_GET['aust_node'], 'chave'=>'aprovacao'));
?>

<p><a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a></p>
<h2><?php echo $aust->leNomeDaEstrutura($_GET['aust_node']);?></h2>
<p>A seguir você vê a lista de registros sob o cadastro "<?php echo $aust->leNomeDaEstrutura($_GET['aust_node'])?>".</p>


<?php

//print_r($precisa_aprovacao);

//pr($resultado);


/*
 * FILTROS ESPECIAIS
 */
if( $fields > 0 ){
    $sql = "SELECT valor
            FROM
                cadastros_conf
            WHERE
                tipo='filtros_especiais' AND
                chave='email' AND
                categorias_id='".$_GET["aust_node"]."'
            ";
    $filtroEspecial = $modulo->connection->query($sql);

    if( !empty($filtroEspecial[0]) )
        $filtroEspecial = $filtroEspecial[0]["valor"];

    if( !empty($filtroEspecial) ){
        $sql = "SELECT
                    t.".$filtroEspecial."
                FROM
                    ".$tabela." as t
                GROUP BY
                    t.".$filtroEspecial."
                ORDER BY t.id DESC
                ";
        $email = $modulo->connection->query($sql);
        foreach( $email as $valor ){
            $emails[] = $valor['email'];
        }

        ?>
        Emails: <input type="text" size="25" value="<?php echo implode("; ", $emails) ?>" />
        <br clear="all" />
        <?php

    }
}

/*
 * Começa a listagem
 */

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET['section'];?>&action=actions&aust_node=<?php echo $_GET['aust_node'];?>">

    <?php
    if( !empty($resultado) ){ ?>
        <?php
        /*
         * SEARCH
         *
         * Mostra resultados somente se existirem no banco de dados
         */
        if( $modulo->getStructureConfig("has_search") ){
            ?>
            <div class="content_search">
                <strong>Buscar:</strong>
                <input type="text" id="search_query_input" onkeyup="cadastroSearch( $('#search_query_input'), <?php echo $this->austNode;?>);" />
                <select id="search_field" onchange="cadastroSearch( $('#search_query_input'), <?php echo $this->austNode;?>);">
                    <option value="&all&">Buscar em Todos</option>
                    <?php
                    foreach( $search_fields as $physicalName=>$humanName ){
                        ?>
                        <option value="<?php echo $physicalName; ?>"><?php echo $humanName; ?></option>
                        <?php
                    }
                    ?>
                </select>
                <?php
                tt("A pesquisa é realizada no valor digitado dos campos. Se você quer buscar o valor de um campo específico, selecione-o ao lado.");
                ?>
                <button onclick="cadastroSearch($('.content_search #search_query_input'), <?php echo $this->austNode;?>); return false;">Pesquisar</button>
                
                <span id="loading_image" style="display: none;">
                    <img style="top: 2px; height: 13px; position: relative;" src="<?php echo IMG_DIR; ?>loading.gif" />
                </span>

                <script type="text/javascript">
                $('.content_search #search_query_input').bind('keypress', function(e) {
                    if(e.keyCode==13){
                        return false;
                    }
                });
                </script>
            </div>
            <?php
        }
        /*
         *
         * PAINEL DE CONTROLE
         *
         */
        ?>
        <?php
        if( $permissoes->canEdit($austNode) ){
            ?>
            <div class="painel_de_controle">Selecionados:
            <?php
            /*
             * Se este cadastro precisa de aprovação, mostra botão para aprovar usuário
             */
            if($precisa_aprovacao['valor'] == '1'){ ?>
                <input type="submit" name="aprovar" value="Aprovar" />
                <?php
            }

            /*
             * Pode excluir?
             */
            if( $permissoes->canDelete($austNode) ){
                ?>
                <input type="submit" name="deletar" value="Deletar" />
                <?php
            }
            ?>
            </div>
            <?php
        } // fim de canEdit()

        ?>

        <div id="listing_table">
        <?php
        include($modulo->getIncludeFolder().'/view/mod/listing_table.php');
        ?>
        </div>
                
    <?php
    } // fim da tabela
    /*
     * Não há resultados, tabela vazia
     */
    else {
        echo "<p>";
        echo "<strong>Não há dados salvos ainda.</strong>";
        echo "</p>";
    }

    ?>

</form>

<?php
/*
 * PAGINAÇÃO
 * mostra painel de navegação para paginação
 */

    //$sql = $modulo->getSQLForListing($categorias);
    $total_registros = $modulo->totalRows;
	$page = $modulo->page();

    $total_paginas = $total_registros/$modulo->defaultLimit;
    $prev = $page - 1;
    $next = $page + 1;
    // se página maior que 1 (um), então temos link para a página anterior
    if ($page > 1) {
        $prev_link = ' <a href="adm_main.php?section='.$_GET['section'].'&action='.$_GET['action'].'&aust_node='.$_GET['aust_node'].'&pagina='.$prev.'">Anterior</a>';
    } else { // senão não há link para a página anterior
        $prev_link = "Anterior";
    }
    // se número total de páginas for maior que a página corrente,
    // então temos link para a próxima página
    if ($total_paginas > $page) {
        $next_link = ' <a href="adm_main.php?section='.$_GET['section'].'&action='.$_GET['action'].'&aust_node='.$_GET['aust_node'].'&pagina='.$next.'">Próxima</a>';
    } else {
    // senão não há link para a próxima página
        $next_link = "Próxima";
    }

    // vamos arredondar para o alto o número de páginas  que serão necessárias para exibir todos os
    // registros. Por exemplo, se  temos 20 registros e mostramos 6 por página, nossa variável
    // $total_paginas será igual a 20/6, que resultará em 3.33. Para exibir os  2 registros
    // restantes dos 18 mostrados nas primeiras 3 páginas (0.33),  será necessária a quarta página.
    // Logo, sempre devemos arredondar uma  fração de número real para um inteiro de cima e isto é
    // feito com a  função ceil()/
    $total_paginas = ceil($total_paginas);
    if($total_paginas > 1){
        $painel = "";
        for ($x=1; $x<=$total_paginas; $x++) {
            if ($x == $page) {
                // se estivermos na página corrente, não exibir o link para visualização desta página
                $painel .= " $x ";
            } else {
               $painel .= ' <a href="adm_main.php?section='.$_GET['section'].'&action='.$_GET['action'].'&aust_node='.$_GET['aust_node'].'&pagina='.$x.'">'.$x.'</a> ';
            }
        }
        // exibir painel na tela
        echo '<div class="paginacao"><strong>Navegação</strong>: '.$prev_link.' | '.$painel.' | '.$next_link.' </div>';
    }

?>

<p style="margin-top: 15px;">
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>