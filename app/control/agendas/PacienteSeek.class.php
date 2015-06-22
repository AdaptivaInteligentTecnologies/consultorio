<?php
/**
 * City Seek
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class PacienteSeek extends TWindow
{
    private $form;      // form
    private $datagrid;  // datagrid
    private $pageNavigation;
    private $parentForm;
    private $loaded;
    
    /**
     * constructor method
     */
    public function __construct()
    {
        parent::__construct();
        parent::setTitle('Localizar Paciente');
        parent::setSize(500,400);
        new TSession;
        
        // creates the form
        $this->form = new TForm('form_agendar_paciente_Seek');
        // creates the table
        $table = new TTable;
        
        // add the table inside the form
        $this->form->add($table);
        
        // create the form fields
        $pts_nome                           = new TEntry('pts_nome');
        $pts_data_nascimento     = new TDate('pts_data_nascimento');
        $pts_data_nascimento->setMask('dd/mm/yyyy');
        $pts_data_nascimento->setSize(100);
        
        
        // keep the session value
        $pts_nome->setValue(TSession::getValue('agendar_paciente_nome'));
        $pts_data_nascimento->setValue(TSession::getValue('agendar_paciente_data_nascimento'));
        
        // add the field inside the table
        $row=$table->addRow();
        $row->addCell(new TLabel('Data de nascimento:'));
        $row->addCell($pts_data_nascimento);
        $row=$table->addRow();
        $row->addCell(new TLabel('Nome:'));
        $row->addCell($pts_nome);
        
        // create a find button
        $find_button = new TButton('search');
        // define the button action
        $find_button->setAction(new TAction(array($this, 'onSearch')), 'Localizar');
        $find_button->setImage('ico_find.png');
        
        // add a row for the find button
        $row=$table->addRow();
        $row->addCell($find_button);
        
        // define wich are the form fields
        $this->form->setFields(array($pts_nome,$pts_data_nascimento, $find_button));
        
        // create the datagrid
        $this->datagrid = new TDataGrid;
        
        // create the datagrid columns
        $column_grid_pts_id                                  = new TDataGridColumn('pts_id',    'Id',   'right',   70);
        $column_grid_pts_nome                          = new TDataGridColumn('pts_nome',  'Nome', 'left',   220);
        $column_grid_pts_data_nascimento    = new TDataGridColumn('pts_data_nascimento', 'DT Nascimento', 'left',     100);
        $column_grid_pts_data_nascimento->setTransformer(array($this, 'formatDate'));
        
        $order1= new TAction(array($this, 'onReload'));
        $order2= new TAction(array($this, 'onReload'));
        $order3= new TAction(array($this, 'onReload'));
        
        $order1->setParameter('order', 'pts_id');
        $order2->setParameter('order', 'pts_nome');
        $order3->setParameter('order', 'pts_data_nascimento');
        
        // define the column actions
        $column_grid_pts_id->setAction($order1);
        $column_grid_pts_nome->setAction($order2);
        $column_grid_pts_data_nascimento->setAction($order3);
        
        // add the columns inside the datagrid
        $this->datagrid->addColumn($column_grid_pts_id);
        $this->datagrid->addColumn($column_grid_pts_nome);
        $this->datagrid->addColumn($column_grid_pts_data_nascimento);
        
        
        // create one datagrid action
        $action1 = new TDataGridAction(array($this, 'onSelect'));
        $action1->setLabel('Selecionar');
        $action1->setImage('ico_apply.png');
        $action1->setField('pts_id');
        
        // add the action to the datagrid
        $this->datagrid->addAction($action1);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigator
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // create a table for layout
        $table = new TTable;
        // create a row for the form
        $row = $table->addRow();
        $row->addCell($this->form);
        
        // create a row for the datagrid
        $row = $table->addRow();
        $row->addCell($this->datagrid);
        
        // create a row for the page navigator
        $row = $table->addRow();
        $row->addCell($this->pageNavigation);
        
        // add the table inside the page
        parent::add($table);
    }
    
    public function formatDate($date, $object)
    {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }
    
    /**
     * Register a filter in the session
     */
    function onSearch()
    {
        // get the form data
        $data = $this->form->getData();
        
        TSession::setValue('agendar_paciente_id', NULL);
        TSession::setValue('agendar_paciente_nome', NULL);
        TSession::setValue('agendar_paciente_data_nascimento', NULL);
        
        
        
        // check if the user has filled the fields
            if (isset($data->pts_nome) AND $data->pts_nome)
        {
            $filter = new TFilter('pts_nome', 'like', "%{$data->pts_nome}%");
            TSession::setValue('agendar_paciente_nome', $filter);
        }
        if (isset($data->pts_data_nascimento) AND $data->pts_data_nascimento)
        {
            // cria um filtro pelo conteúdo digitado
             $filter = new TFilter(" to_char(pts_data_nascimento,'DD/MM/YYYY')",'=', "{$data->pts_data_nascimento}" ); // create the filter
            TSession::setValue('agendar_paciente_data_nascimento',$filter);
        }
        
        // put the data back to the form
        $this->form->setData($data);
        
        // redefine the parameters for reload method
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * Load the datagrid with the database objects
     */
    function onReload($param = NULL)
    {
        try
        {
            // start database transaction
            TTransaction::open('consultorio');
            
            // create a repository for City table
            $repository = new TRepository('Paciente');
            $limit = 10;
            
            // creates a criteria
            $criteria = new TCriteria;
            
            if (empty($param['order']))
            {
                $param['order'] = 'pts_id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            
            if (TSession::getValue('agendar_paciente_data_nascimento'))
            {
                
                // filter by born's date
                $criteria->add(TSession::getValue('agendar_paciente_data_nascimento'));
            }
            
            
            if (TSession::getValue('agendar_paciente_nome'))
            {
                // filter by name
                $criteria->add(TSession::getValue('agendar_paciente_nome'));
            }
            
            
            
            // load the objects according to the criteria
            $pacientes = $repository->load($criteria,FALSE);
            $this->datagrid->clear();
            if ($pacientes)
            {
                foreach ($pacientes as $paciente)
                {
                    // add the objects inside the datagrid
                    $this->datagrid->addItem($paciente);
                }
            }
            
            // clear the criteria
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // commit and closes the database transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // exceptions
        {
            // show the error message
            new TMessage('error', '<b>Erro</b> ' . $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Executed when the user chooses the record
     */
    function onSelect($param)
    {
        try
        {
            $key = $param['key'];
            TTransaction::open('consultorio');
            
            // load the active record
            $paciente = new Paciente($key);
            
            // closes the transaction
            TTransaction::close();
            
            $object = new StdClass;
            $object->aps_pts_id         = $paciente->pts_id;
            $object->aps_pts_nome  = $paciente->pts_nome;
            
            TForm::sendData('agenda_paciente_form', $object);
            parent::closeWindow(); // closes the window
        }
        catch (Exception $e) // em caso de exceção
        {
            // clear fields
            $object = new StdClass;
            $object->pts_id          = '';
            $object->pts_nome   = '';
            TForm::sendData('agenda_paciente_form', $object);
            
            // undo pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Shows the page
     */
    function show()
    {
        // if the datagrid was not loaded yet
        if (!$this->loaded)
        {
            $this->onReload();
        }
        parent::show();
    }
}
