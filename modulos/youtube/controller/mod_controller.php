<?php
/**
 * Descrição deste arquivo
 *
 * @package ModController
 * @name nome
 * @author Alexandre <chavedomundo@gmail.com>
 * @since v0.1.5 24/06/2009
 */

class ModController extends ModsController
{

    public function listing(){
        $this->set('h1', 'Listando conteúdo: '.$this->aust->leNomeDaEstrutura($_GET['aust_node']) );
        
        $nome_modulo = $this->aust->LeModuloDaEstrutura($_GET['aust_node']);
        $sql = "SELECT
                    id,nome
                FROM
                    ".Aust::$austTable."
                WHERE
                    id='".$_GET['aust_node']."'";


        $query = $this->modulo->connection->query($sql);
        $this->set('cat', $query[0]['nome'] );

        $categorias = $this->aust->LeCategoriasFilhas('',$_GET['aust_node']);
        $categorias[$_GET['aust_node']] = 'Estrutura';

        /*
         * PAGINAÇÃO
         */
        /*
         * Página atual
         */
        $pagina = (empty($_GET['pagina'])) ? $pagina = 1 : $pagina = $_GET['pagina'];
        /*
         * Resultados por página
         */
        $num_por_pagina = '20';
        $this->set('numPorPagina', $num_por_pagina);//($config->LeOpcao($nome_modulo.'_paginacao')) ? $config->LeOpcao($nome_modulo.'_paginacao') : '10';

        /*
         * SQL para listagem
         */
        $params = array(
            'austNode' => $categorias,
            'pagina' => $pagina,
            'resultadosPorPagina' => $num_por_pagina
        );
        $sql = $this->modulo->loadSql($params);
        $this->set('sql', $sql );

        /*
         * Query com resultado
         */
        $this->set('query', $this->modulo->connection->query($sql) );
        
    }

    public function create(){


        $this->render('form');
    }

    public function edit(){

        
        $this->render('form');
    }

    public function save(){
        
    }
    
}
?>