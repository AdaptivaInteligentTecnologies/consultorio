<?php
/**
 * ProcedimentoProfissionalList Listing
 * @author  <your name here>
 */
class ProcedimentoProfissionalList extends TPage
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
        $this->form = new TForm('form_search_ProcedimentoProfissional');
        $this->form->class = 'tform'; // CSS class
        
        // creates a table
        $table = new TTable;
        $table-> width = '100%';
        $this->form->add($table);
        
        // add a row for the form title
        $row = $table->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Procedimentos Médicos') )->colspan = 2;
        

        // create the form fields
        $pms_id                         = new TEntry('pms_id');
        $pms_descricao                  = new TEntry('pms_descricao');

        // define the sizes
        $pms_id->setSize(100);
        $pms_descricao->setSize(200);


        // add one row for each form field
        $table->addRowSet( new TLabel('ID:'), $pms_id );
        $table->addRowSet( new TLabel('Descrição:'), $pms_descricao );


        $this->form->setFields(array($pms_id,$pms_descricao));


        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ProcedimentoProfissional_filter_data') );
        
        // create two action buttons to the form
        $find_button = TButton::create('find', array($this, 'onSearch'), _t('Find'), 'ico_find.png');
        $new_button  = TButton::create('new',  array('ProcedimentoProfissionalForm', 'onEdit'), _t('New'), 'ico_new.png');
        
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
        $pms_id   = new TDataGridColumn('pms_id', 'ID', 'right', 100);
        $pms_descricao   = new TDataGridColumn('pms_descricao', 'Descrição', 'left', 200);
        $pms_valor   = new TDataGridColumn('pms_valor', 'Valor', 'left',100);
        $pms_cor   = new TDataGridColumn('pms_cor', 'COR', 'center', 30);
        $pms_cor  ->setTransformer(array($this, 'mostraCor'));


        // add the columns to the DataGrid
        $this->datagrid->addColumn($pms_id);
        $this->datagrid->addColumn($pms_descricao);
        $this->datagrid->addColumn($pms_valor);
        $this->datagrid->addColumn($pms_cor);

        
        // creates two datagrid actions
        $action1 = new TDataGridAction(array('ProcedimentoProfissionalForm', 'onEdit'));
        $action1->setLabel(_t('Edit'));
        $action1->setImage('ico_edit.png');
        $action1->setField('pms_id');
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        $action2->setLabel(_t('Delete'));
        $action2->setImage('ico_delete.png');
        $action2->setField('pms_id');
        
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
    
    /**
     * method onInlineEdit()
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('consultorio'); // open a transaction with database
            $object = new ProcedimentoProfissional($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
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
        TSession::setValue('ProcedimentoProfissionalList_filter_pms_id',   NULL);
        TSession::setValue('ProcedimentoProfissionalList_filter_pms_descricao',   NULL);
        TSession::setValue('ProcedimentoProfissionalList_filter_pms_cor',   NULL);

        if (isset($data->pms_id) AND ($data->pms_id)) {
            $filter = new TFilter('pms_id', 'like', "%{$data->pms_id}%"); // create the filter
            TSession::setValue('ProcedimentoProfissionalList_filter_pms_id',   $filter); // stores the filter in the session
        }


        if (isset($data->pms_descricao) AND ($data->pms_descricao)) {
            $filter = new TFilter('pms_descricao', 'like', "%{$data->pms_descricao}%"); // create the filter
            TSession::setValue('ProcedimentoProfissionalList_filter_pms_descricao',   $filter); // stores the filter in the session
        }


        if (isset($data->pms_cor) AND ($data->pms_cor)) {
            $filter = new TFilter('pms_cor', 'like', "%{$data->pms_cor}%"); // create the filter
            TSession::setValue('ProcedimentoProfissionalList_filter_pms_cor',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('ProcedimentoProfissional_filter_data', $data);
        
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
            
            // creates a repository for ProcedimentoProfissional
            $repository = new TRepository('ProcedimentoProfissional');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'pms_id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('ProcedimentoProfissionalList_filter_pms_id')) {
                $criteria->add(TSession::getValue('ProcedimentoProfissionalList_filter_pms_id')); // add the session filter
            }


            if (TSession::getValue('ProcedimentoProfissionalList_filter_pms_descricao')) {
                $criteria->add(TSession::getValue('ProcedimentoProfissionalList_filter_pms_descricao')); // add the session filter
            }


            if (TSession::getValue('ProcedimentoProfissionalList_filter_pms_cor')) {
                $criteria->add(TSession::getValue('ProcedimentoProfissionalList_filter_pms_cor')); // add the session filter
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
    
    
    public function mostraCor($cor, $object)
    {
        //print_r($cor);
        $div = new TElement('div');
        $div->class = 'tcolor-icon';
        $div->style = "width: 50%; height:50%; background-color: {$cor}";
        return $div;
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
            $object = new ProcedimentoProfissional($key, FALSE); // instantiates the Active Record
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
