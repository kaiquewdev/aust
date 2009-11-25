<?php
/**
 * Este é o arquivo contendo a página inicial do sistema
 */
?>

<h1>Bem-vindo!</h1>
<p>Com este gerenciador, você pode administrar todo o conteúdo do seu site.</p>

<div class="painel-metade">
    <div class="painel">
        <div class="titulo">
            <h2>Opções rápidas</h2>
        </div>
        <div class="corpo">

            <?php
            /**
             * Listagem das estruturas cadastradas no sistema na tela inicial
             *
             * Contém o atalho para as estrutura
             */
            $param = array(
                        'orderby' => 'ORDER BY tipo'
                        );
            $est = $aust->LeEstruturasParaArray($param);

            //pr($est);
            //pr($categoriasPermitidas);
            
            if(count($est) > 0){
                ?>
                <p>Abaixo, as estruturas instaladas. Passe o mouse.</p>

                <table width="100%" summary="Lista de estruturas do site">
                <col width="160"/>
                <col />
                <thead>
                <tr>
                    <th>Estrutura</th>
                    <th>Tipo</th>
                </tr>
                </thead>
                <tbody>

                <?php
                foreach($est as $key=>$valor){

                    /**
                     * Verifica se usuário tem permissão de acesso a esta
                     * estrutura
                     */
                    //pr( $categoriasPermitidas);
                    if($permissoes->verify( array( 'estrutura' => $valor['id'], 'permissoes' => $categoriasPermitidas ))){

                        /**
                         * Inclui módulo apropriado
                         */
                        include(THIS_TO_BASEURL.'modulos/'.$valor['tipo'].'/'.MOD_CONFIG);
                        echo '<tr>';
                        echo '<td valign="top">';
                        echo '<a href="#" class="link_pai_do_est_options" onmouseover="javascript: est_options('.$valor['id'].')">'.$valor['nome'].'</a>';
                        echo '<div class="est_options" id="est_options_'.$valor['id'].'">';
                        if(is_array($modInfo['opcoes'])){
                            $i = 0;
                            foreach($modInfo['opcoes'] as $opcao=>$opcaonome){
                                if($i > 0) echo ', ';
                                echo '<a href="adm_main.php?section=conteudo&action='.$opcao.'&aust_node='.$valor['id'].'">'.$opcaonome.'</a>';
                                $i++;
                            }
                        }
                        echo '</div>';
                        echo '</td>';
                        echo '<td valign="top"><span class="est_options_info" id="est_options_info_'.$valor['id'].'">('.$valor['tipo'].')</span>'.'</td>';
                        echo '</tr>';
                    }
                }
                ?>
                </tbody>
                </table>
                <?php
            } else {
                ?>
                <p>Não há estruturas cadastradas. Contacte seu administrador.</p>
                <?php
            }
            ?>

        </div>
        <div class="rodape"></div>
    </div>

    <?php /*<div class="painel">
        <div class="titulo">
            <h2>Últimos conteúdos</h2>
        </div>
        <div class="corpo">
            <p>
                Selecione abaixo a categoria-chefe, o nome da estrutura (ex.: Notícias, Artigos, Arquivos) e o módulo adequado.
            </p>
        </div>
        <div class="rodape"></div>
    </div>*/?>
</div>



<div class="painel-metade painel-dois">
    <?php /*<div class="painel">
        <div class="titulo">
            <h2>Pendências</h2>
        </div>
        <div class="corpo">


        </div>
        <div class="rodape"></div>
    </div>*/?>

<?php
/**
 * RESUMO DOS MÓDULOS INSTALADOS
 *
 * Mostra conteúdos cadastrados ultimamente
 */
/**
 * Carregas os módulos em uma array contendo algumas informações sobre ele, como endereço físico
 */
$painel = $modulos->LeModulosParaArray();
/**
 * Se há módulos a serem carregados
 */
if( !empty($painel) ){
    /**
     * Faz um loop por cada módulo
     */
    foreach($painel AS $chave=>$valor){
        
        /**
         * Se o módulo possui arquivo de resumo para a interface frontal do CMS
         * Carrega arquivos e mostra dados das arrays que vem.
         *
         * Aqui há uma verificação se o módulo possui responser
         *
         */
        $responser = false;
        unset($configFile);
        if( is_file($valor['pasta'].'/index.php') ){
            include($valor['pasta'].'/index.php');
            include($valor['pasta'].'/'.MOD_CONFIG);
            if( (
                    is_bool($modInfo['responser'])
                    AND $modInfo['responser'] == true
                )
                OR $modInfo['responser']['actived'] == true )
            {
                $responser = true;
            }
        }

        if( $responser == true ){


            //include($valor['pasta'].'/front_painel.inc.php');

            /**
             * Toma conteúdos
             */
            $conteudo = $modulo->retornaResumo();
            //pr($conteudo);

            
            if(!empty($conteudo)){
                ?>
                <div class="painel front_painel">
                    <div class="titulo">
                        <h2><?php
                        /**
                         * Título do módulo
                         */
                        echo $modInfo['nome'];

                        ?></h2>
                    </div>
                    <div class="corpo">
                        <?php
                        /**
                         * Mostra cada categoria com seus textos
                         */
                        /**
                         * Escreve parágrafo introdutório ao módulo
                         */
                        if(!empty($conteudo['intro'])){
                            echo '<p class="intro">'.$conteudo['intro'].'</p>';
                        }

                        /**
                         * Faz um loop por cada conteúdo enviado pelo módulo e
                         * escreve uma lista na tela com títulos
                         */
                        foreach($conteudo as $chave=>$valor){
                            if(is_int($chave)){
                                $params = array(
                                    'estrutura' => $chave,
                                    'permissoes' => $categoriasPermitidas,
                                );
                                if($permissoes->verify($params)){
                                    echo '<ul>';
                                    echo '<li class="titulo">'.$valor['titulo'].'</li>';
                                    if(is_array($valor['conteudo'])){
                                        foreach($valor['conteudo'] as $cChave=>$cValor){
                                            echo '<li class="conteudo">';
                                            /**
                                             * Escreve $chave no aust_node porque o valor é da estrutura.
                                             */
                                            echo '<a href="adm_main.php?section=conteudo&action=editar&aust_node='.$chave.'&w='.$cValor['id'].'">'.$cValor['titulo'].'</a>';
                                            echo '</li>';
                                        }
                                    }
                                    echo '</ul>';
                                }
                            }
                        }
                        //echo $moduloConf['nome'];

                        ?>

                    </div>
                    <div class="rodape"></div>
                </div>
                <?php
            }
            
            unset($modulo);
            //pr($conteudo);
            unset($conteudo);
        }
    }
}
?>

</div>


<?php
/*
	if(($escala == "webmaster" OR $escala == "administrador") AND $senha <> "senha"){
	?>
        <div style="width: 690px; minheight: 20px; border: 1px dotted silver; padding: 5px; margin-top: 15px;">
        	<a name="senhas" style="color: black;"><h3>Senhas</h3></a>
        	<a href="adm_main.php?senha=senha#senhas">Clique aqui para ver as senhas dos usuários</a>
        </div>
    <?
	} else if(($escala == "webmaster" OR $escala == "administrador") AND $senha == "senha") {
		include("inc/inc_admins_passw_retrieve.php");
	}
 *
 */
?>