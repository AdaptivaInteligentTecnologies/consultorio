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
		// Verifica se est� logado
		if ( TSession::getValue('logged') != TRUE )
		{
			throw new Exception(_t('Not logged'), 1);
			
		}
*/
		// Inicializa a sessão desta página
		new TSession;

		new TMastHead('M�dulos','Lista de m�dulos dispon�veis no sistema');

		$this->form = new TForm('frmListaModulos');

		// Cria o campo para pesquisa
		$txtNomeModuloSearch = new TEntry('nomeModulo');

		// Características do campo criado
		$txtNomeModuloSearch->setSize(200);
		$txtNomeModuloSearch->setValue(TSession::getValue('txtNomeModuloSearch'));

		
		// Cria a tabela para oraganização dos campos de pesquisas
		$table = new TTable;

		// Adiciona os campos de pesquisa à tabela
		$row = $table->addRow();
		
		$cell =  $row->addCell('M�dulo: ');
		$cell->width = 25;

		$cell =  $row->addCell(' ');

		$cell->width = (PHP_SAPI == 'cli'?40:80);
		
		$row->addCell($txtNomeModuloSearch);

		// Adiciona os campos de buscas oraganizados ao formulário

		// Cria o botão localizar
		$btnSearch = new TButton('btnSearch');
		$btnSearch->setAction( new TAction( array( $this,'onSearch' )),'Localizar' );
		$btnSearch->setImage('ico_find.png');

		// Cria o botão exportar CVS
		$btnCVS = new TButton('btnCVS');
		$btnCVS->setAction(new TAction(array($this,'onExportCVS')),'Exportar CVS' );
		$btnCVS->setImage('ico_print.png');

		// Cria o botão novo
		$btnNew = new TButton('btnNew');
		$btnNew->setAction(new TAction(array($this,'onNew')),'Novo' );
		$btnNew->setImage('ico_new.png');

		$row->addCell($btnSearch);
		$row->addCell($btnCVS);
		$row->addCell($btnNew);

		$frame = new TFrame();
		$frame->setLegend('Par�metros de busca');
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
