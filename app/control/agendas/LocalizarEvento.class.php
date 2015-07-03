<?php

use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TCombo;
use Adianti\Control\TWindow;
use Adianti\Widget\Container\TJQueryDialog;
use adianti\widget\dialog\TToast;
use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Control\TAction;
//namespace control\agendas;

class LocalizarEvento extends TWindow
{
    private $form;     // registration form
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    
    function __construct()
    {

        parent::__construct();
        parent::setTitle('Localizar Paciente');
        parent::setSize(450,500);
        
        // creates the form
        $this->form = new TForm('form_localizar_evento');
        $this->form->class = 'tform'; // CSS class
        
        // creates a table
        $table = new TTable;
        $table-> width = '100%';
        $this->form->add($table);
        
        // add a row for the form title
        $row = $table->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Localizar paciente na agenda') )->colspan = 2;
        
        $aps_pfs_id = new TCombo('aps_pfs_id'); // criar método para carregar os profissionais e um método onChange para selecionar apenas os pacientes desse profissional
        $aps_pfs_id->setSize(300);
        $aps_pfs_id->addItems($this->carregaProfissionais());
        $actionChange = new TAction(array($this,'onProfissionalChange'));
        $aps_pfs_id->setChangeAction($actionChange);
        
        //$itemsProfissionais = $this->carregaProfissionais();
        
        $table->addRowSet( new TLabel('Profissional:'), $aps_pfs_id );
        
        $aps_nome_paciente = new TEntry('aps_nome_paciente');
        $aps_nome_paciente->setSize(300);
        $table->addRowSet( new TLabel('Paciente:'), $aps_nome_paciente );
        
        $this->form->setFields(array($aps_nome_paciente,$aps_pfs_id));        
        
        $find_button        = TButton::create('find', array($this, 'onSearch'), 'Localizar', 'ico_find.png');
        $close_button       = TButton::create('new',  array($this, 'onClose'), _t('Close'), 'ico_close.png');
        $find_today_button  = TButton::create('findToday',  array($this, 'onSearchForToday'), 'Pacientes de hoje', 'ico_find.png');
        
        $this->form->addField($find_button);
        $this->form->addField($close_button);
        $this->form->addField($find_today_button);
        
        $buttons_box = new THBox;
        $buttons_box->add($find_button);
        $buttons_box->add($close_button);
        $buttons_box->add($find_today_button);
        
        // add a row for the form action
        $row = $table->addRow();
        $row->class = 'tformaction'; // CSS class
        $row->addCell($buttons_box)->colspan = 2;
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setHeight(300);
        
        // creates the datagrid columns
        $aps_id_grid                = new TDataGridColumn('aps_id', 'ID', 'right', 60);
        $order_aps_id_grid         = new TAction(array($this, 'onReload'));
        $order_aps_id_grid->setParameter('order', 'aps_id');
        $aps_id_grid->setAction($order_aps_id_grid);
        
        $aps_nome_paciente_grid     = new TDataGridColumn('aps_nome_paciente', 'Paciente', 'left', 200);
        $order_aps_nome_paciente_grid         = new TAction(array($this, 'onReload'));
        $order_aps_nome_paciente_grid->setParameter('order', 'aps_nome_paciente');
        $aps_nome_paciente_grid->setAction($order_aps_nome_paciente_grid);
        
        $aps_data_agenda_grid       = new TDataGridColumn('aps_data_agendada', 'Data', 'left', 100);
        $aps_data_agenda_grid->setTransformer(array($this, 'formatDate'));
        $order_aps_data_agenda_grid         = new TAction(array($this, 'onReload'));
        $order_aps_data_agenda_grid->setParameter('order', 'aps_data_agendada');
        $aps_data_agenda_grid->setAction($order_aps_data_agenda_grid);
        
        $aps_hora_agenda_grid       = new TDataGridColumn('aps_hora_agendada', 'Hora', 'left', 100);
        $aps_hora_agenda_grid->setTransformer(array($this, 'formatTime'));
        
        $aps_pfs_id_grid       = new TDataGridColumn('nomeProfissional', 'Profissional', 'left', 200);
        $order_aps_pfs_id_grid = new TAction(array($this,'onReload'));
        $order_aps_pfs_id_grid->setParameter('order','aps_pfs_id');
        $aps_pfs_id_grid->setAction($order_aps_pfs_id_grid);
        
        // add the columns to the DataGrid
        $this->datagrid->addColumn($aps_id_grid);
        $this->datagrid->addColumn($aps_nome_paciente_grid);
        $this->datagrid->addColumn($aps_data_agenda_grid);
        $this->datagrid->addColumn($aps_hora_agenda_grid);
        $this->datagrid->addColumn($aps_pfs_id_grid);
        
        
        // creates two datagrid actions
        $action1 = new TDataGridAction(array($this, 'onSelect'));
        $action1->setLabel(_t('Select'));
        $action1->setImage('ico_apply.png');
        $action1->setField('aps_id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        
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
     * method onSearch()
     * Register the filter in the session when the user performs a search
     */
    function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
    
        // clear session filters
        TSession::setValue('LocalizarEvento_filter_aps_id',   NULL);
        TSession::setValue('LocalizarEvento_filter_aps_nome_paciente',   NULL);
        TSession::setValue('LocalizarEvento_filter_aps_pfs_id',   NULL);
        TSession::setValue('LocalizarEvento_filter_search_today',   NULL);
            
        if (isset($data->aps_id) AND ($data->aps_id)) {
            $filter = new TFilter('aps_id', 'like', "%{$data->aps_id}%"); // create the filter
            TSession::setValue('LocalizarEvento_filter_aps_id',   $filter); // stores the filter in the session
        }
    
        if (isset($data->aps_nome_paciente) AND ($data->aps_nome_paciente)) {
            $filter = new TFilter('upper(aps_nome_paciente)', 'like', "%".strtoupper($data->aps_nome_paciente)."%"); // create the filter
            TSession::setValue('LocalizarEvento_filter_aps_nome_paciente',   $filter); // stores the filter in the session
        }
    
        if (isset($data->aps_pfs_id) AND ($data->aps_pfs_id) ) {
            $filter = new TFilter('aps_pfs_id', '=', "{$data->aps_pfs_id}"); // create the filter
            //new TToast('info','Informação',$filter);
            TSession::setValue('LocalizarEvento_filter_aps_pfs_id',   $filter); // stores the filter in the session
        }
    
        // fill the form with data again
        $this->form->setData($data);
    
        // keep the search data in the session
        TSession::setValue('LocalizarEvento_filter_data', $data);
    
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    function onSearchForToday()
    {
        // get the search form data
        $filter = new TFilter('aps_data_agendada', '=', "'".date('d/m/Y')."'"); // create the filter
        TSession::setValue('LocalizarEvento_filter_search_today',   $filter); // stores the filter in the session
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
            
            // creates a repository for StatusAgendamento
            $repository = new TRepository('AgendaPaciente');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'aps_id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('LocalizarEvento_filter_aps_id')) {
                $criteria->add(TSession::getValue('LocalizarEvento_filter_aps_id')); // add the session filter
            }


            if (TSession::getValue('LocalizarEvento_filter_aps_nome_paciente')) {
                $criteria->add(TSession::getValue('LocalizarEvento_filter_aps_nome_paciente')); // add the session filter
            }


            if (TSession::getValue('LocalizarEvento_filter_aps_pfs_id')) {
                $criteria->add(TSession::getValue('LocalizarEvento_filter_aps_pfs_id')); // add the session filter
            }
            
            if (TSession::getValue('LocalizarEvento_filter_search_today')) {
                $criteria->add(TSession::getValue('LocalizarEvento_filter_search_today')); // add the session filter
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
     * Executed when the user chooses the record
     */
    function onSelect($param)
    {
        try
        {
            
            $key = $param['key'];
            
            TTransaction::open('consultorio');
            
            // load the active record
            $agendaPaciente = new AgendaPaciente($key);
            
            // closes the transaction
            TTransaction::close();

            //TScript::create("$('#calendar').fullCalendar('gotoDate', $.fullCalendar.moment('".$agendaPaciente->aps_data_agendada."'));");
            TScript::create("$('#calendar').fullCalendar('gotoDate', $.fullCalendar.moment('".TDate::parseDate($agendaPaciente->aps_data_agendada)."'));");
            
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
    public function onClose(){
        //TJQueryDialog::closeAll();
        parent::closeWindow();
    }
    
    public function carregaProfissionais()
    {
        try
        {
            TTransaction::open('consultorio');
            $repository = new TRepository('Profissional');
            $profissionais = $repository->where('pfs_id', '<>', '0')->load();
        
            foreach ($profissionais as $profissional)
            {
                $mProfissional[$profissional->pfs_id]=$profissional->pfs_nome;
            }
            TTransaction::close();
            return $mProfissional;
            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    static public function onProfissionalChange()
    {
        //new TToast('info', 'Teste','Mensagem de teste',2000);
        
    }
    
    public function formatDate($date, $object)
    {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }
    
    public function formatTime($date, $object)
    {
        $dt = new DateTime($date);
        return $dt->format('H:i');
    }
    
    /*
    public function nomeProfissional($data, $object)
    {
        //new TToast('info', 'Teste',var_dump($object),2000);
        TTransaction::open('consultorio');
        $repository = new TRepository('Profissional');
        $profissionais = $repository->where('pfs_id', '=', $data)->load(); 
        TTransaction::close();
        return $profissionais[0]->pfs_nome;
    }
    */
        
    
    
}

?>