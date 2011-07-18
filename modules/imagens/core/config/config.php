<?php
/**
 * Configurações
 *
 * Arquivo contendo informações sobre este módulo
 *
 * @package Modulos
 * @name Config
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since v0.1.5, 30/05/2009
 */
/**
 * Variável contendo as configurações deste módulo
 *
 * @global array $GLOBALS['modInfo']
 * @name $modInfo
 */

$modInfo = array(
    /**
     * Cabeçalho
     *
     * Informações sobre o próprio módulo, como nome, descriçao, entre outros
     */
    /**
     * É arquitetura MVC
     */
    'mvc' => true,
    /**
     * 'nome': Nome humano do módulo
     */
    'name' => 'Imagens',
    /**
     * 'className': Classe oficial do módulo
     */
    'className' => 'Imagens',
    /**
     * 'descricao': Descrição que facilita compreender a função do módulo
     */
    'description' => 'Módulo gerenciador de imagens e banners',
    /**
     * 'estrutura': Se pode ser instalada como estrutura (Textos podem)
     */
    'estrutura' => true,
    /**
     * 'somenteestrutura': É uma estrutura somente, sem categorias? (cadastros,
     * por exemplo)
     */
    'somenteestrutura' => true,
    /**
     * 'embed': É do tipo embed?
     */
    'embed' => false,
    /**
     * 'embedownform': É do tipo embed que tem seu próprio formulário?
     */
    'embedownform' => false,



    /**
     * RESPONSER
     */
    /**
     * Se possui método de leitura de resumo
     */
    'responser' => array(
        'actived' => false,
    ),

    /**
     * Opções de gerenciamento de conteúdo
     *
     * A opções a seguir dizem respeito a qualquer ação que envolva
     * a interação do módulo com conteúdo.
     */
    /**
     * Opções de gerenciamento deste módulo
     *
     */
    'actions' => array(
        'create' => 'Inserir',
        'listing' => 'Listar',
    ),
	
	'viewmodes' => array('thumbs', 'list'),
	
	'configurations' => array(
	    /*
	     * Salva arquivo no DB (por padrão, salva arquivos fisicamente)
	     */
	    'save_files_to_db' => array(
	        "propriedade" => "save_files_to_db", // nome da propriedade
	        "value" => "",
	        "label" => "Salvar arquivo no Banco de Dados?",
	        "inputType" => "checkbox",
			'help' => 'Por padrão, os arquivos são salvos fisicamente. Arquivos Flash '.
			 		  'são obrigatoriamente salvos fisicamente, mesmo que esta opção '.
					  'esteja selecionada.',
	    ),
	
   		'ordenate' => array(
	        "value" => "",
	        "label" => "Ordenado",
	        "inputType" => "checkbox",
	    ),
	    /*
	     * Resumo
	     */
	    'resumo' => array(
	        "propriedade" => "resumo", // nome da propriedade
	        "value" => "",
	        "label" => "Tem resumo?",
	        "inputType" => "checkbox",
	    ),
	    /*
	     * Tem data de expiração (quando deixará de aparecer a imagem).
	     */
	    'expireTime' => array(
	        "value" => "",
	        "label" => "Tem expireTime?",
	        "inputType" => "checkbox",
	    ),
	    /*
	     * Tem Descrição?
	     */
	    'descricao' => array(
	        "value" => "",
	        "label" => "Tem descrição?",
	        "inputType" => "checkbox",
	    ),
		    /*
		     * Tem editor rico de texto em descrição?
		     */
		    'description_has_rich_editor' => array(
		        "value" => "",
		        "label" => "Descrição tem editor de texto rico?",
		        "inputType" => "checkbox",
		    ),
	    /*
	     * Tem Link?
	     */
	    'link' => array(
	        "value" => "",
	        "label" => "Tem link?",
	        "inputType" => "checkbox",
	    ),		
	    /*
	     * Tem seleção categoria?
	     */
	    'category_selection' => array(
	        "value" => "",
	        "label" => "Seleção de categoria?",
	        "inputType" => "checkbox",
	    ),		
	    /*
	     * Pode criar categorias?
	     */
	    'category_creation' => array(
	        "value" => "",
	        "label" => "Botão criar categoria?",
	        "inputType" => "checkbox",
			'help' => 'Só funcionará se puder selecionar a categoria.'
		),
	    /*
	     * Permite SWF?
	     */
	    'allow_flash_upload' => array(
	        "value" => "",
	        "label" => "Permite upload de Flash (.swf)?",
	        "inputType" => "checkbox",
			'help' => 'Arquivos Flash têm a extensão swf.'
		)
	),
    /*
     * Se não há valor, substitui campo vazio na listagem
     * pelos valores abaixo
     */
    'replaceFieldsValueIfEmpty' => array(
        'titulo' => '[Sem título]',
    ),

    /**
     * RESPONSER
     *
     * A seguir, as configurações do módulo para que este possa apresentar um
     * resumo a qualquer requisitante (chamado responser).
     */
    'arquitetura' => array(
        'table' => 'textos',
        'foreignKey' => 'categoria',
    ),


    /**
     * '': 
     */
    '' => '',

    /**
     * CABEÇALHOS DE LISTAGEM
     */
    'contentHeader' => array(
        'campos' => array(
            'adddate','titulo','node'
        ),
        'camposNome' => array(
            'Data','Título','Categoria'
        ),
    )
);
?>
