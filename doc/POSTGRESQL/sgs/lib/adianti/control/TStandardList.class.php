<?php
/**
 * Standard page controller for listings
 *
 * @version    1.0
 * @package    control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TStandardList extends TPage
{
    protected $form;            // registration form
    protected $datagrid;        // listing
    protected $pageNavigation;  // pagination component
    protected $activeRecord;    // Active Record class name
    protected $filterField;     // filtering field name
    protected $database;        // Database name
    protected $loaded;
    protected $limit;
    protected $operator;
    protected $order;
    protected $direction;
    protected $criteria;
    
    public function __construct()
    {
        parent::__construct();
        $this->operator = 'like';
    }
    
    /**
     * method setDatabase()
     * Define the database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }
    
    /**
     * method setLimit()
     * Define the record limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
    
    /**
     * Define the default order
     * @param $order The order field
     * @param $directiont the order direction (asc, desc)
     */
    public function setDefaultOrder($order, $direction = 'asc')
    {
        $this->order = $order;
        $this->direction = $direction;
    }
    
    /**
     * method setOperator()
     * Define the filtering operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }
    
    /**
     * method setActiveRecord()
     * Define wich Active Record class will be used
     */
    public function setActiveRecord($activeRecord)
    {
        if (is_subclass_of($activeRecord, 'TRecord'))
        {
            $this->activeRecord = $activeRecord;
        }
        else
        {
            throw new Exception(TAdiantiCoreTranslator::translate('The class ^1 must be subclass of ^2', $activeRecord, 'TRecord'));
        }
    }
    
    /**
     * method setFilterField()
     * Define wich field will be used for filtering
     */
    public function setFilterField($filterField)
    {
        $this->filterField = $filterField;
    }
    
    /**
     * method setCriteria()
     * Define the criteria
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * method onInlineEdit()
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            // open a transaction with database
            TTransaction::open($this->database);
            
            // instantiates object {ACTIVE_RECORD}
            $class = $this->activeRecord;
            
            // instantiates object
            $object = new $class($key);
            
            // deletes the object from the database
            $object->{$field} = $value;
            $object->store();
            
            // close the transaction
            TTransaction::close();
            
            // reload the listing
            $this->onReload($param);
            // shows the success message
            new TMessage('info', "Record Updated");
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
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // check if the user has filled the form
        if (isset($data->{$this->filterField}) AND $data->{$this->filterField})
        {
            // creates a filter using what the user has typed
            if (stristr($this->operator, 'like'))
            {
                $filter = new TFilter($this->filterField, $this->operator, "%{$data->{$this->filterField}}%");
            }
            else
            {
                $filter = new TFilter($this->filterField, $this->operator, $data->{$this->filterField});
            }
            
            // stores the filter in the session
            TSession::setValue($this->activeRecord.'_filter', $filter);
            TSession::setValue($this->activeRecord.'_'.$this->filterField, $data->{$this->filterField});
        }
        else
        {
            TSession::setValue($this->activeRecord.'_filter', NULL);
            TSession::setValue($this->activeRecord.'_'.$this->filterField, '');
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
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database
            TTransaction::open($this->database);
            
            // instancia um repositório
            $repository = new TRepository($this->activeRecord);
            $limit = isset($this->limit) ? $this->limit : 10;
            // creates a criteria
            $criteria = isset($this->criteria) ? $this->criteria : new TCriteria;
            if ($this->order)
            {
                $criteria->setProperty('order',     $this->order);
                $criteria->setProperty('direction', $this->direction);
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue($this->activeRecord.'_filter'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue($this->activeRecord.'_filter'));
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
            
            if (isset($this->pageNavigation))
            {
                $this->pageNavigation->setCount($count); // count of records
                $this->pageNavigation->setProperties($param); // order, page
                $this->pageNavigation->setLimit($limit); // limit
            }
            
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
    public function onDelete($param)
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
    public function Delete($param)
    {
        try
        {
            // get the parameter $key
            $key=$param['key'];
            // open a transaction with database
            TTransaction::open($this->database);
            
            $class = $this->activeRecord;
            
            // instantiates object
            $object = new $class($key);
            
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
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
?>