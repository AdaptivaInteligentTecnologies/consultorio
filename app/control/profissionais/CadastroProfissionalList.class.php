<?php
use Adianti\Widget\Menu\TMenu;
use Adianti\Widget\Dialog\TMessage;
/**
 * CadastroProfissionalList Listing
 * @author  Sebastião Carnegie
 */


class CadastroProfissionalList extends TPage
{

    private $form;     // registration form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
/*        $titulo = new TElement('h2');
        $titulo->add('Cadastro Médico');
        $hr  = new TElement('hr');
        
        
        parent::add($titulo);
        parent::add($hr);
  */      
        parent::__construct();
        
       
        // creates the form
        $this->form         = new TForm('form_search_cadastro_Profissional_list');
        $this->form->class  = 'tform';
        
        // creates a table
        $table              = new TTable;
        
        $table->style       = 'width:100%';
        
        //$table->addRowSet( new TLabel('Lista de médicos cadastrados'), '' )->class = 'tformtitle';
        
        $row=$table->addRow();
        $row->class = 'tformtitle';
        $cell = $row->addCell( new TLabel('Lista de profissionais cadastrados'));
        $cell->colspan = 2;
        
        
        
        // add the table inside the form
        $this->form->add($table);
        
        // create the form fields
        
        $profissionalNome            = new TEntry('pfs_nome');
        $profissionalNome->setValue(TSession::getValue('session_listagem_cadastro_Profissional_nome'));
        
        // add a row for the filter field
        
        $row=$table->addRow();
        $cell = $row->addCell(new TLabel('Nome: '));
        $cell = $row->addCell($profissionalNome);
        $cell->colspan = 2;
        
        
        // create two action buttons to the form
        $find_button        = new TButton('find');
        $new_button         = new TButton('new');

        // define the button actions
        $find_button->setAction(new TAction(array($this, 'onSearch')), _t('Find'));
        $find_button->setImage('ico_find.png');
        
        $new_button->setAction(new TAction(array('CadastroProfissionalForm', 'onEdit')), _t('New'));
        $new_button->setImage('ico_new.png');
        
        // add a row for the form actions
        $container          = new THBox;
        $container->add($find_button);
        $container->add($new_button);
        

        $row=$table->addRow();
        $row->class = 'tformaction';
        $cell = $row->addCell( $container );
        $cell->colspan = 2;
        
        // define wich are the form fields
        $this->form->setFields(array($profissionalNome, $find_button, $new_button));
        
        // creates a DataGrid
        $this->datagrid     = new TDataGrid;
        $this->datagrid->setHeight(320);
        $this->datagrid->style = 'width: 100%';
        
        // creates the datagrid columns
        $profissionalIdGrid               = new TDataGridColumn('pfs_id'             , 'ID',   'left');
        $profissionalNomeGrid             = new TDataGridColumn('pfs_nome'           , 'Nome', 'left');
        $profissionalConselhoGrid         = new TDataGridColumn('pfs_numero_conselho', 'CONSELHO',  'left');
        $profissionalNumeroConselhoGrid   = new TDataGridColumn('pfs_numero_conselho', 'NR. CONSELHO',  'left');
        $profissionalConselhoUfGrid       = new TDataGridColumn('uf_conselho'        , 'UF',   'left');
        $profissionalCnesGrid             = new TDataGridColumn('pfs_cnes'           , 'CNES', 'left');
        
        // add the columns to the DataGrid
        $this->datagrid->addColumn($profissionalIdGrid   );
        $this->datagrid->addColumn($profissionalNomeGrid );
        $this->datagrid->addColumn($profissionalConselhoGrid  );
        $this->datagrid->addColumn($profissionalNumeroConselhoGrid  );
        $this->datagrid->addColumn($profissionalConselhoUfGrid);
        $this->datagrid->addColumn($profissionalCnesGrid );
        
        // creates the datagrid column actions
        $order_profissionalId        = new TAction(array($this, 'onReload'));
        $order_profissionalId->setParameter('order', 'pfs_id');
        $profissionalIdGrid->setAction($order_profissionalId);
        
        $order_profissionalNome      = new TAction(array($this, 'onReload'));
        $order_profissionalNome->setParameter('order', 'pfs_nome');
        $profissionalNomeGrid->setAction($order_profissionalNome);
        
        // creates two datagrid actions
        $action1            = new TDataGridAction(array('CadastroProfissionalForm', 'onEdit'));
        $action1->setLabel(_t('Edit'));
        $action1->setImage('ico_edit.png');
        $action1->setField('pfs_id');
        
        $action2            = new TDataGridAction(array($this, 'onDelete'));
        $action2->setLabel(_t('Delete'));
        $action2->setImage('ico_delete.png');
        $action2->setField('pfs_id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // creates the page structure using a table
        $table                = new TTable;
        $table->style = 'width: 80%';
        $table->addRow()->addCell(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $table->addRow()->addCell($this->form);
        $table->addRow()->addCell($this->datagrid);
        $table->addRow()->addCell($this->pageNavigation);
        
        // add the table inside the page
        parent::add($table);

    }
    
    /**
     * method onSearch()
     * Register the filter in the session when the user performs a search
     */
    function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        TSession::setValue('session_listagem_cadastro_Profissional_nome_filter',   NULL);
        TSession::setValue('session_listagem_cadastro_Profissional_nome', '');
        
        // check if the user has filled the form
        
        if ( $data->pfs_nome )
        {
            // creates a filter using what the user has typed
            $filter = new TFilter('upper(pfs_nome)', 'like', '%'.strtoupper($data->pfs_nome).'%');
            
            // stores the filter in the session
            TSession::setValue('session_listagem_cadastro_Profissional_nome_filter', $filter);
            TSession::setValue('session_listagem_cadastro_Profissional_nome', $data->pfs_nome);
        }
        
        // fill the form with data again
        $this->form->setData($data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * method onReload()
     * Load the datagrid with the database objects
     */
    function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'permission'
            TTransaction::open('consultorio');
            
            //print_r($param,true);
            
            if( ! isset($param['order']) )
                $param['order'] = 'pfs_id';
            
            // creates a repository for System_user
            $repository = new TRepository('Profissional');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('session_listagem_cadastro_Profissional_id_filter'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('session_listagem_cadastro_Profissional_id_filter'));
            }
            if (TSession::getValue('session_listagem_cadastro_Profissional_nome_filter'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('session_listagem_cadastro_Profissional_nome_filter'));
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method onDelete()
     * executed whenever the user clicks at the delete button
     * Ask if the user really wants to delete the record
     */
    function onDelete($param)
    {

        
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * method Delete()
     * Delete a record
     */
    function Delete($param)
    {
        
        try
        {
            // get the parameter $key
            $key=$param['key'];
            // open a transaction with database 'permission'
            TTransaction::open('consultorio');
            
            // instantiates object System_user
            $object = new Profissional($key);
            
            // deletes the object from the database
            $object->delete($key);
            
            // close the transaction
            TTransaction::close();
            
            // reload the listing
            $this->onReload( $param );
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'));
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method show()
     * Shows the page
     */
    function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded)
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
?>