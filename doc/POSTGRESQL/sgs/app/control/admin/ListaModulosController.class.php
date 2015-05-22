<?php

class ListaModulosController extends TPage
{

	
	protected $form;
	protected $datagrid;
	protected $pageNavigation;
	protected $loaded;



	public function __construct()
	{
		parent::__construct();

/*
		// Verifica se está logado
		if ( TSession::getValue('logged') != TRUE )
		{
			throw new Exception(_t('Not logged'), 1);
			
		}
*/
		// Inicializa a sessÃ£o desta pÃ¡gina
		new TSession;

		new TMastHead('Módulos','Lista de módulos disponíveis no sistema');

		$this->form = new TForm('frmListaModulos');

		// Cria o campo para pesquisa
		$txtNomeModuloSearch = new TEntry('nomeModulo');

		// CaracterÃ­sticas do campo criado
		$txtNomeModuloSearch->setSize(200);
		$txtNomeModuloSearch->setValue(TSession::getValue('txtNomeModuloSearch'));

		
		// Cria a tabela para oraganizaÃ§Ã£o dos campos de pesquisas
		$table = new TTable;

		// Adiciona os campos de pesquisa Ã  tabela
		$row = $table->addRow();
		
		$cell =  $row->addCell('Módulo: ');
		$cell->width = 25;

		$cell =  $row->addCell(' ');

		$cell->width = (PHP_SAPI == 'cli'?40:80);
		
		$row->addCell($txtNomeModuloSearch);

		// Adiciona os campos de buscas oraganizados ao formulÃ¡rio

		// Cria o botÃ£o localizar
		$btnSearch = new TButton('btnSearch');
		$btnSearch->setAction( new TAction( array( $this,'onSearch' )),'Localizar' );
		$btnSearch->setImage('ico_find.png');

		// Cria o botÃ£o exportar CVS
		$btnCVS = new TButton('btnCVS');
		$btnCVS->setAction(new TAction(array($this,'onExportCVS')),'Exportar CVS' );
		$btnCVS->setImage('ico_print.png');

		// Cria o botÃ£o novo
		$btnNew = new TButton('btnNew');
		$btnNew->setAction(new TAction(array($this,'onNew')),'Novo' );
		$btnNew->setImage('ico_new.png');

		$row->addCell($btnSearch);
		$row->addCell($btnCVS);
		$row->addCell($btnNew);

		$frame = new TFrame();
		$frame->setLegend('Parâmetros de busca');
		$frame->add($table);

		$this->form->add($frame);
		$this->form->setFields(array($txtNomeModuloSearch,$btnSearch,$btnCVS,$btnNew));

//		$table = new TTable;
//		$table->addRow()->addCell($this->form);
		parent::add($this->form);

	}

function onSearch()
{
	//
}

function onExportCVS()
{
	//
}

function onNew()
{
	//
}
}
?>
