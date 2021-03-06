<?php
/**
 * EspecialidadeProfissionalList Listing
 * @author  <your name here>
 */

//namespace control\Profissional;


use Adianti\Control\TPage;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Container\TTable;
use Adianti\Widget\Container\THBox;
use Adianti\Control\TAction;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Util\TXMLBreadCrumb;



class EspecialidadeProfissionalList extends TPage
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
        $this->form = new TForm('form_search_especialidadeProfissional');
        $this->form->class = 'tform';
        
        // creates a table
        $table = new TTable;
        
        $table->style = 'width:100%';
        
        $table->addRowSet( new TLabel('Especialidades Médicas'), '' )->class = 'tformtitle';
        
        // add the table inside the form
        $this->form->add($table);
        
        // create the form fields
        $id = new TEntry('ems_id');
        $id->setValue(TSession::getValue('session_especialidadeProfissionalId'));
        
        $descricao = new TEntry('ems_descricao');
        $descricao->setValue(TSession::getValue('session_especialidadeProfissionalDescricao'));
        
        // add a row for the filter field
        $table->addRowSet(new TLabel('ID:'), $id);
        $table->addRowSet(new TLabel('Descrição' . ': '), $descricao);
        
        // create two action buttons to the form
        $find_button = new TButton('find');
        $new_button  = new TButton('new');
        // define the button actions
        $find_button->setAction(new TAction(array($this, 'onSearch')), _t('Find'));
        $find_button->setImage('ico_find.png');
        
        $new_button->setAction(new TAction(array('EspecialidadeProfissionalForm', 'onEdit')), _t('New'));
        $new_button->setImage('ico_new.png');
        
        // add a row for the form actions
        $container = new THBox;
        $container->add($find_button);
        $container->add($new_button);

        $row=$table->addRow();
        $row->class = 'tformaction';
        $cell = $row->addCell( $container );
        $cell->colspan = 2;
        
        // define wich are the form fields
        $this->form->setFields(array($id, $descricao, $find_button, $new_button));
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setHeight(320);
        $this->datagrid->style = 'width: 100%';
        
        // creates the datagrid columns
        $id          = new TDataGridColumn('ems_id',    'ID', 'right');
        $descricao   = new TDataGridColumn('ems_descricao', 'Descrição', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($id);
        $this->datagrid->addColumn($descricao);


        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'ems_id');
        $id->setAction($order_id);

        $order_descricao= new TAction(array($this, 'onReload'));
        $order_descricao->setParameter('order', 'ems_descricao');
        $descricao->setAction($order_descricao);



        // inline editing
        $descricao_edit = new TDataGridAction(array($this, 'onInlineEdit'));
        $descricao_edit->setField('ems_id');
        $descricao->setEditAction($descricao_edit);
        
       
        // creates two datagrid actions
        $action1 = new TDataGridAction(array('EspecialidadeProfissionalForm', 'onEdit'));
        $action1->setLabel(_t('Edit'));
        $action1->setImage('ico_edit.png');
        $action1->setField('ems_id');
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        $action2->setLabel(_t('Delete'));
        $action2->setImage('ico_delete.png');
        $action2->setField('ems_id');
        
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
        $table = new TTable;
        $table->style = 'width: 80%';
        $table->addRow()->addCell(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $table->addRow()->addCell($this->form);
        $table->addRow()->addCell($this->datagrid);
        $table->addRow()->addCell($this->pageNavigation);
        
        // add the table inside the page
        parent::add($table);
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
            
            // open a transaction with database 'permission'
            TTransaction::open('consultorio');
            
            
            
            // instantiates object System_user
            $object = new EspecialidadeProfissional($key);
            // deletes the object from the database
            $object->{$field} = $value;
            $object->store();
            
            // close the transaction
            TTransaction::close();
            
            // reload the listing
            $this->onReload($param);
            // shows the success message
            new TMessage('info', _t('Record Updated'));
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
     * method onSearch()
     * Register the filter in the session when the user performs a search
     */
    function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        TSession::setValue('session_especialidadeProfissionalIdFilter',   NULL);
        TSession::setValue('session_especialidadeProfissionalDescricaoFilter',   NULL);
        
        TSession::setValue('session_especialidadeProfissionalId', '');
        TSession::setValue('session_especialidadeProfissionalDescricao', '');
        
        // check if the user has filled the form
        if ( $data->id )
        {
            // creates a filter using what the user has typed
            $filter = new TFilter('ems_id', '=', "{$data->id}");
            
            // stores the filter in the session
            TSession::setValue('session_especialidadeProfissionalIdFilter',   $filter);
            TSession::setValue('session_especialidadeProfissionalId', $data->id);
        }
        if ( $data->ems_descricao )
        {
            // creates a filter using what the user has typed
            $filter = new TFilter('ems_descricao', 'like', "%{$data->ems_descricao}%");
            
            // stores the filter in the session
            TSession::setValue('session_especialidadeProfissionalDescricaoFilter',   $filter);
            TSession::setValue('session_especialidadeProfissionalDescricao', $data->ems_descricao);
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
            
            if( ! isset($param['order']) )
                $param['order'] = 'ems_id';
            
            // creates a repository for System_user
            $repository = new TRepository('EspecialidadeProfissional');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('session_especialidadeProfissionalIdFilter'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('session_especialidadeProfissionalIdFilter'));
            }
            if (TSession::getValue('session_especialidadeProfissionalDescricaoFilter'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('session_especialidadeProfissionalDescricaoFilter'));
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
            $object = new EspecialidadeProfissional($key);
            
            // deletes the object from the database
            $object->delete();
            
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