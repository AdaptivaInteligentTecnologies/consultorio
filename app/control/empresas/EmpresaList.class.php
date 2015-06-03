<?php
/**
 * EmpresaList Listing
 * @author  <your name here>
 */
class EmpresaList extends TPage
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
        parent::__construct();
        
        // creates the form
        $this->form = new TForm('form_search_Empresa');
        $this->form->class = 'tform'; // CSS class
        
        // creates a table
        $table = new TTable;
        $table-> width = '100%';
        $this->form->add($table);
        
        // add a row for the form title
        $row = $table->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Cadastro de Empresa') )->colspan = 2;
        

        // create the form fields
        $emp_id                         = new TEntry('emp_id');
        $emp_nome_fantasia              = new TEntry('emp_nome_fantasia');
        $emp_razao_social               = new TEntry('emp_razao_social');
        $emp_cnpj                       = new TEntry('emp_cnpj');


        // define the sizes
        $emp_id->setSize(100);
        $emp_nome_fantasia->setSize(200);
        $emp_razao_social->setSize(200);
        $emp_cnpj->setSize(200);


        // add one row for each form field
        $table->addRowSet( new TLabel('ID:'), $emp_id );
        $table->addRowSet( new TLabel('Nome Fantasia:'), $emp_nome_fantasia );
        $table->addRowSet( new TLabel('Razão Social:'), $emp_razao_social );
        $table->addRowSet( new TLabel('CNPJ:'), $emp_cnpj );


        $this->form->setFields(array($emp_id,$emp_nome_fantasia,$emp_razao_social,$emp_cnpj));


        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Empresa_filter_data') );
        
        // create two action buttons to the form
        $find_button = TButton::create('find', array($this, 'onSearch'), _t('Find'), 'ico_find.png');
        $new_button  = TButton::create('new',  array('EmpresaForm', 'onEdit'), _t('New'), 'ico_new.png');
        
        
        $this->form->addField($find_button);
        $this->form->addField($new_button);
        
        $buttons_box = new THBox;
        $buttons_box->add($find_button);
        $buttons_box->add($new_button);
        
        // add a row for the form action
        $row = $table->addRow();
        $row->class = 'tformaction'; // CSS class
        $row->addCell($buttons_box)->colspan = 2;
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setHeight(320);
        

        // creates the datagrid columns
        $emp_id   = new TDataGridColumn('emp_id', 'ID', 'right', 100);
        $emp_nome_fantasia   = new TDataGridColumn('emp_nome_fantasia', 'Nome Fantasia', 'left', 200);
        $emp_razao_social   = new TDataGridColumn('emp_razao_social', 'Razão Social', 'left', 200);
        $emp_cnpj   = new TDataGridColumn('emp_cnpj', 'CNPJ', 'left', 200);


        // add the columns to the DataGrid
        $this->datagrid->addColumn($emp_id);
        $this->datagrid->addColumn($emp_nome_fantasia);
        $this->datagrid->addColumn($emp_razao_social);
        $this->datagrid->addColumn($emp_cnpj);


        // creates two datagrid actions
        $action1 = new TDataGridAction(array('EmpresaForm', 'onEdit'));
        $action1->setLabel(_t('Edit'));
        $action1->setImage('ico_edit.png');
        $action1->setField('emp_id');
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        $action2->setLabel(_t('Delete'));
        $action2->setImage('ico_delete.png');
        $action2->setField('emp_id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // create the page container
        $container = TVBox::pack( $this->form, $this->datagrid, $this->pageNavigation);
        parent::add($container);
    }
    
    
    public function onEdit($param)
    {
        //
    }
    
    /**
     * method onSearch()
     * Register the filter in the session when the user performs a search
     */
    function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('EmpresaList_filter_emp_id',   NULL);
        TSession::setValue('EmpresaList_filter_emp_nome_fantasia',   NULL);
        TSession::setValue('EmpresaList_filter_emp_razao_social',   NULL);
        TSession::setValue('EmpresaList_filter_emp_cnpj',   NULL);

        if (isset($data->emp_id) AND ($data->emp_id)) {
            $filter = new TFilter('emp_id', 'like', "%{$data->emp_id}%"); // create the filter
            TSession::setValue('EmpresaList_filter_emp_id',   $filter); // stores the filter in the session
        }


        if (isset($data->emp_nome_fantasia) AND ($data->emp_nome_fantasia)) {
            $filter = new TFilter('emp_nome_fantasia', 'like', "%{$data->emp_nome_fantasia}%"); // create the filter
            TSession::setValue('EmpresaList_filter_emp_nome_fantasia',   $filter); // stores the filter in the session
        }


        if (isset($data->emp_razao_social) AND ($data->emp_razao_social)) {
            $filter = new TFilter('emp_razao_social', 'like', "%{$data->emp_razao_social}%"); // create the filter
            TSession::setValue('EmpresaList_filter_emp_razao_social',   $filter); // stores the filter in the session
        }


        if (isset($data->emp_cnpj) AND ($data->emp_cnpj)) {
            $filter = new TFilter('emp_cnpj', 'like', "%{$data->emp_cnpj}%"); // create the filter
            TSession::setValue('EmpresaList_filter_emp_cnpj',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Empresa_filter_data', $data);
        
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
            // open a transaction with database 'consultorio'
            TTransaction::open('consultorio');
            
            // creates a repository for Empresa
            $repository = new TRepository('Empresa');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'emp_id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('EmpresaList_filter_emp_id')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_emp_id')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_emp_nome_fantasia')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_emp_nome_fantasia')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_emp_razao_social')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_emp_razao_social')); // add the session filter
            }


            if (TSession::getValue('EmpresaList_filter_emp_cnpj')) {
                $criteria->add(TSession::getValue('EmpresaList_filter_emp_cnpj')); // add the session filter
            }

            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
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
            $key=$param['key']; // get the parameter $key
            TTransaction::open('consultorio'); // open a transaction with database
            $object = new Empresa($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            $this->onReload( $param ); // reload the listing
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted')); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * method show()
     * Shows the page
     */
    function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
