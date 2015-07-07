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
use Adianti\Database\TCriteria;
use Adianti\Registry\TSession;
use adianti\widget\dialog\TToast;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TLabel;
use Adianti\Database\TFilter;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Form\TEntry;
use Adianti\Database\TRepository;
use Adianti\Database\TExpression;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Base\TScript;



class FilaAtendimentoList extends TPage
{
    private $form;     // registration form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    
    private $totalPorDia;
    private $totalAtendidos;
    private $totalAguardando;
    
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TForm('form_fila_atendimento');
        $this->form->class = 'tform';
        
        // creates a table
        $table = new TTable;
        $table->style = 'width:100%';
        $table->addRowSet( new TLabel('Atendimento a pacientes'), '' )->class = 'tformtitle';
        
        // add the table inside the form
        
        $this->form->add($table);
        
        // create a header for consult
        //$hbox = new THBox('cabecalhoFilaAtendimento');
        
        $cabecalhoQuantitativo = new TTable('cabecalhoQuantitativo');
        $cabecalhoQuantitativo->class = 'fila-atendimento-tbl-cabecalho-quantitativo';

        $r1 = $cabecalhoQuantitativo->addRow();
        $cell2 = $r1->addCell('Totais de pacientes');
        $cell2->class = 'titulo';
        $cell2->colspan = 3;
        
        
        $cabecalhoQuantitativo->addRowSet('Para o dia','Atendidos','Aguardando')->class = 'titulo';
        
        $this->totalPorDia = new TEntry('totalPorDia');
        $this->totalPorDia->setSize(120);
        $this->totalPorDia->setEditable(FALSE);
        
        //$totalPorDia->class = '';
        
        
        $this->totalAtendidos = new TEntry('totalAtendidos');
        $this->totalAtendidos->setSize(120);
        $this->totalAtendidos->setEditable(FALSE);
        
        $this->totalAguardando = new TEntry('totalAguardando');
        $this->totalAguardando->setSize(120);
        $this->totalAguardando->setEditable(FALSE);
        
        $cabecalhoQuantitativo->addRowSet($this->totalPorDia,$this->totalAtendidos,$this->totalAguardando)->class = 'total';
        
        $btnFilterDate = new TButton('btnFilterButton');
        $btnFilterDate->setAction(new TAction(array($this,'onSearch')),'Buscar');
        $btnFilterDate->setImage('ico_find.png');
        
        $tblCabecalhoFilaAtendimento = new TTable;
        $tblCabecalhoFilaAtendimento->style = 'width:100%';
        $row = $tblCabecalhoFilaAtendimento->addRow();
        $row->class = 'tformtitle';
        $row->addCell( new TLabel('Atendimento a pacientes'))->colspan = 2;

        $dataFilaAtendimento = new TDate('dataAtendimento');
        //$actDataFilaAtendimentoExit = new TAction(array($this,'onSearch2'));
        $dataFilaAtendimento->setMask('dd/mm/yyyy');
        $dataFilaAtendimento->setValue(date('d/m/Y'));
        $dataFilaAtendimento->setSize(100);
        //$dataFilaAtendimento->setExitAction($actDataFilaAtendimentoExit);
        
        $cell1 = $tblCabecalhoFilaAtendimento->addRow();
        $cell1->addMultiCell(new TLabel('Data de Atendimento:'),$dataFilaAtendimento,$btnFilterDate);
        $cell1->addCell($cabecalhoQuantitativo);
        $this->form->setFields(array(
            $dataFilaAtendimento,
            $btnFilterDate,
        ));
        
        //$hbox->addRowSet($tblCabecalhoFilaAtendimento,'teste2','teste3');
        $this->form->add($tblCabecalhoFilaAtendimento);
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setHeight(320);
        $this->datagrid->style = 'width: 100%';
        
        // creates the datagrid columns
        $cns_id              = new TDataGridColumn('cns_id',    'ID', 'center');
        $cns_pts_id          = new TDataGridColumn('cns_pts_id', 'Id do paciente', 'center');
        $cns_pts_id          = new TDataGridColumn('paciente',    'Paciente', 'left');
        $cns_idade           = new TDataGridColumn('idade',    'Idade', 'left');
        $cns_pms_id          = new TDataGridColumn('procedimento',    'Procedimento', 'center');
        $cns_hora_chegada    = new TDataGridColumn('hora_chegada',    'Hora de chegada', 'center');
        $cns_pne             = new TDataGridColumn('pne',    'P.N.E', 'center');
        $cns_status          = new TDataGridColumn('status',    'Status', 'center');
        
        
        
        // add the columns to the DataGrid
        //$this->datagrid->addColumn($cns_id);
        $this->datagrid->addColumn($cns_pts_id);
        $this->datagrid->addColumn($cns_idade);
        $this->datagrid->addColumn($cns_pms_id);
        $this->datagrid->addColumn($cns_hora_chegada);
        $this->datagrid->addColumn($cns_pne);
        $this->datagrid->addColumn($cns_status);

        // creates the datagrid column actions
        
        // creates two datagrid actions
        $action1 = new TDataGridAction(array($this, 'onSelect'));
        $action1->setLabel('Selecionar');
        $action1->setImage('ico_apply.png');
        //$action1->setField('cns_id');
        $action1->setField('cns_pts_id');
        
        $order_cns_id = new TAction(array($this, 'onReload'));
        $order_cns_id->setParameter('order', 'cns_id');
        $order_cns_id->setParameter('direction', 'asc');
        $cns_id->setAction($order_cns_id);
        
        $action2 = new TDataGridAction(array($this, 'onShowDatail'));
        $action2->setLabel('Detalhes do paciente');
        $action2->setImage('ico_find.png');
        $action2->setField('cns_pts_id');
        
        
        
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
    
    
    
    public static function onDataFilaAtendimentoExit($param)
    {
        /*
        if ( isset($param['dataAtendimento']) and ( $param['dataAtendimento'] != '' )) 
        {
            $filter = new TFilter('cns_data_consulta', '=', "{$param['dataAtendimento']}");
            TSession::setValue('session_data_fila_atendimento_filter',$filter);
            TSession::setValue('session_data_fila_atendimento_data',$filter);
            
            $param=array();
            $param['offset']    =0;
            $param['first_page']=1;
            FilaAtendimentoList::onReload($param);
        }
        */        
    }
    
    
    public function onSelect($param)
    {
        //new TToast($param['key']);
        //ConsultaForm::onReload();
        //__adianti_post_data(\'form_agendar_paciente\', \'class=LocalizarEvento&method=onSearch\');
        TScript::create("__adianti_post_data('form_fila_atendimento', 'class=ConsultaForm&method=onReload&key={$param['key']}');");
    }
    
    /**
     * method onSearch()
     * Register the filter in the session when the user performs a search
     */
    
    function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        TSession::setValue('session_data_fila_atendimento_filter',   NULL);
        
        // check if the user has filled the form
        if ( ! $data->dataAtendimento )
        {
            $data->dataAtendimento = date("d/m/Y");
        }
        
        $filter = new TFilter('cns_data_consulta', '=', "{$data->dataAtendimento}");
        TSession::setValue('session_data_fila_atendimento_filter', $filter);
        TSession::setValue('session_data_fila_atendimento_data'  , $data->dataAtendimento);
        
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
                $param['order'] = 'cns_id';
            
            if( ! isset($param['direction']) )
                $param['direction'] = 'asc';
            
            
            // creates a repository for System_user
            
            $repository = new TRepository('Consulta');
            
            
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('session_data_fila_atendimento_filter'))
            {
                $criteria->add(TSession::getValue('session_data_fila_atendimento_filter'));
            }
            else
            {
                $filter = new TFilter('cns_data_consulta', '=', date("d/m/Y"));
                TSession::setValue('session_data_fila_atendimento_filter', $filter);
                TSession::setValue('session_data_fila_atendimento_data'  , date("d/m/Y"));
                $criteria->add(TSession::getValue('session_data_fila_atendimento_filter'));
            }

            // load the objects according to criteria
            $objects = $repository->load($criteria);
            //$this->totalPorDia->setValue(count($objects));
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit

                        
            
            $repositorytotalPorDia = new TRepository('Consulta');
            $this->totalPorDia->setValue(
                $repositorytotalPorDia->where('cns_data_consulta','=',TSession::getValue('session_data_fila_atendimento_data'))->count()
            );
            
            $repositorytotalAtendidos = new TRepository('Consulta');
            $this->totalAtendidos->setValue(
            
                $repositorytotalAtendidos
                    ->where('cns_data_consulta','=',TSession::getValue('session_data_fila_atendimento_data'))
                    ->where('cns_data_hora_ini_consulta','IS NOT','NOESC:NULL')
                    ->where('cns_data_hora_fim_consulta','IS NOT','NOESC:NULL')
                    ->count()
            );
            
            $repositorytotalAguardando = new TRepository('Consulta');
            $this->totalAguardando->setValue(
            
                $repositorytotalAguardando
                    ->where('cns_data_consulta','=',TSession::getValue('session_data_fila_atendimento_data'))
                    ->where('cns_data_hora_ini_consulta','IS','NOESC:NULL')
                    ->count()
            );
            
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

    public function onShowDatail($param)
    {
        //var_dump($param['key']);
        if (isset($param['key']))
        {
          $key = $param['key'];
          $paciente = new Paciente($key);
        }
        $sexo = array("M"=>"Masculino","F"=>"Feminino",""=>"Não informado");
        $message = "
            Nome: <strong>{$paciente->pts_nome}</strong><br />
            Nascido em: {$paciente->pts_data_nascimento}<br />
            Idade: ".TDate::getIdade($paciente->pts_data_nascimento)."<br />
            Mãe: {$paciente->pts_nome_mae}<br />
            Sexo: ".$sexo[$paciente->pts_sexo]; 
        new TMessage('info', $message);
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