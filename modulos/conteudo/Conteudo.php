<?php
/**
 * CLASSE DO MÓDULO
 *
 * Classe contendo funcionalidades deste módulo
 *
 * @package Modulos
 * @name Conteúdos
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since v0.1.5, 30/05/2009
 */
class Conteudo extends Module
{
    public $mainTable = "textos";

    function __construct(){
        parent::__construct(array());
    }

    /**
     * getInstance()
     *
     * Para Singleton
     *
     * @staticvar <object> $instance
     * @return <Conexao object>
     */
    static function getInstance(){
        static $instance;

        if( !$instance ){
            $instance[0] = new get_class();
        }

        return $instance[0];

    }


    /**
     * loadSql()
     *
     * Retorna um SQL para uma listagem genérica dos dados deste módulo.
     *
     * @param <array> $options
     * @return <string>
     */
    public function loadSql($options = array()) {
        /*
         * SET DEFAULT OPTIONS
         */
        require_once(LIB_DATA_TYPES);
        /*
         * Default options
         */
        $categorias = getDataInArray($options, 'categorias');
        $pagina = getDataInArray($options, 'pagina');
        $itens_por_pagina = getDataInArray($options, 'resultadosPorPagina');
        $limit = '';

        $order = ' ORDER BY id DESC';
        /*
         * Gera condições para sql
         */
        if(!empty($categorias)) {
            $where = ' WHERE ';
            $c = 0;
            foreach($categorias as $key=>$valor) {
                if($c == 0)
                    $where = $where . 'categoria=\''.$key.'\'';
                else
                    $where = $where . ' OR categoria=\''.$key.'\'';
                $c++;
            }
        }

        /*
         * Paginação?
         */
        if(!empty($pagina)) {
            $item_atual = ($pagina * $itens_por_pagina) - $itens_por_pagina;
            $limit = " LIMIT ".$item_atual.",".$itens_por_pagina;
        }

        /*
         * Sql para listagem
         */
        $sql = "SELECT
                    id, titulo, visitantes,
                    categoria AS cat,
                    DATE_FORMAT(adddate, '%d/%m/%Y %H:%i') as adddate,
                    (	SELECT
                            nome
                        FROM
                            categorias AS c
                        WHERE
                            id=cat
                    ) AS node
                FROM
                    ".$this->tabela_criar.$where.$order.
                $limit;
        return $sql;

    } // fim getSQLForListing()

    /**
     * save()
     *
     * Salva dados da estrutua.
     *
     * @param <array> $post
     * @return <bool>
     */
    public function save($post = array() ){

        if( empty($post) )
            return false;

        $post['frmtitulo_encoded'] = encodeText($post['frmtitulo']);

        /**
         * @todo - tirar isto daqui
         */
        foreach($post as $key=>$valor) {
            // se o argumento $post contém 'frm' no início
            if(strpos($key, 'frm') === 0) {
                $sqlcampo[] = str_replace('frm', '', $key);
                $sqlvalor[] = $valor;
                // ajusta os campos da tabela nos quais serão gravados dados
                $valor = addslashes($valor);
                if($post['metodo'] == 'criar') {
                    if($c > 0) {
                        $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key);
                        $sqlvalorstr = $sqlvalorstr.",'".$valor."'";
                    } else {
                        $sqlcampostr = str_replace('frm', '', $key);
                        $sqlvalorstr = "'".$valor."'";
                    }
                } else if($post['metodo'] == 'editar') {
                    if($c > 0) {
                        $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key).'=\''.$valor.'\'';
                    } else {
                        $sqlcampostr = str_replace('frm', '', $key).'=\''.$valor.'\'';
                    }
                }

                $c++;
            }
        }

        if( empty($sqlcampostr) ){
            return false;
        }


        if($post['metodo'] == 'criar') {
            $sql = "INSERT INTO
                        textos
                        ($sqlcampostr)
                    VALUES
                        ($sqlvalorstr)";


        } elseif( $post['metodo'] == 'editar' OR
                  $post['w'] > 0 )
        {
            $sql = "UPDATE
                        textos
                    SET
                        $sqlcampostr
                    WHERE
                        id='".$post['w']."'
                    ";
        }

        $query = $this->connection->exec($sql);
        
        if($query !== false) {
            $resultado = true;

            // se estiver criando um registro, guarda seu id para ser usado por módulos embed a seguir
            if($post['metodo'] == 'criar') {
                $post['w'] = $this->connection->conn->lastInsertId();
            }

            $w = $post['w'];
            /*
             *
             * EMBED SAVE
             *
            */
            //include(INC_DIR.'conteudo.inc/embed_save.php');


            /*
             *
             * EMBED SAVE
             *
             */
            $embedData = array(
                'embedModules' => $post['embed'],
                'options' => array(
                    'targetTable' => $post['contentTable'],
                    'w' => $w,
                )
            );
            $this->saveEmbeddedModules($embedData);


        } else {
            $resultado = false;
        }
        return $resultado;
    }
}
?>