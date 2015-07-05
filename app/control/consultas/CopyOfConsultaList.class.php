<?php
use Adianti\Control\TPage;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Container\TTable;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TEntry;
use Adianti\Control\TAction;
use adianti\widget\dialog\TToast;
//namespace control\consultas;

class ConsultaList extends TPage
{

    private $form;
    private $datagrid;    
    private $pageNavigation;
    private $loaded;
        
    function __construct()
    {
        parent::__construct();
        
        $this->form = new TForm('form_consulta_list');
        $this->form->class = 'tform';
        
        $tblFormConsultaList = new TTable;
        $tblFormConsultaList->style = 'width:100%;';

        $tblFormConsultaList->addRowSet( new TLabel('Atendimento ao paciente'), '' )->class = 'tformtitle';
        
        $this->form->add($tblFormConsultaList);
        /*
        $cns_data_consulta            = new TDate('cns_data_consulta');
        $cns_data_consulta->setMask('dd/mm/yyyy');
        $cns_data_consulta->id = 'dataConsulta';
        $cns_data_consulta->setSize(150);
        $cns_data_consulta->setExitAction(new TAction(array($this,'onDataConsultaExit')));
        
        $row = $tblFormConsultaList->addRow();
        $row->addCell(new TLabel('Data da consulta'))->colspan= 1;
        $row->addCell($cns_data_consulta)->colspan=3;
        
        
        $this->form->setFields(
            array(
                $cns_data_consulta,
        
            ));
            */

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setHeight(320);
        $this->datagrid->width = '100%';
        
        // creates the datagrid columns
        $cns_idG                    = new TDataGridColumn('cns_id', 'ID', 'right', 100);
        $cns_pts_idG                = new TDataGridColumn('cns_pts_id', 'pts_id', 'right', 100);
        
        
        //$cns_status_consulta		= new TDataGridColumn('cns_status_consulta', 'Status', 'left', 200);
        //$pts_data_nascimento->setTransformer(array($this, 'formatDate'));
        
        
        // add the columns to the DataGrid
        $this->datagrid->addColumn($cns_idG);
        $this->datagrid->addColumn($cns_pts_idG);
/*
        $this->datagrid->addColumn($cns_pts_nomeG);
        $this->datagrid->addColumn($cns_pms_nomeG);
        $this->datagrid->addColumn($cns_idadeG);
        $this->datagrid->addColumn($cns_data_consultaG);
        
*/        
        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'cns_id');
        $cns_idG->setAction($order_id);
                
        //$this->datagrid->addAction($order_id);
        //$this->datagrid->addColumn($cns_status_consulta);
        
        
        // creates two datagrid actions
/*
        $action1 = new TDataGridAction(array('PacienteForm', 'onEdit'));
        $action1->setLabel(_t('Edit'));
        $action1->setImage('ico_edit.png');
        $action1->setField('pts_id');
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        $action2->setLabel(_t('Delete'));
        $action2->setImage('ico_delete.png');
        $action2->setField('pts_id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
  */      
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        //$container = TVBox::pack( $this->form, $this->datagrid, $this->pageNavigation);
        //parent::add($container);
        
        $this->form->add($tblFormConsultaList);
        
        // creates the page structure using a table
        $tblShow = new TTable;
        $tblShow->style = 'width: 80%';
        $tblShow->addRow()->addCell(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $tblShow->addRow()->addCell($this->form);
        $tblShow->addRow()->addCell($this->datagrid);
        $tblShow->addRow()->addCell($this->pageNavigation);        
        /*
        $row = $tblFormConsultaList->addRow();
        $cell = $row->addCell($this->datagrid);
        $cell->colspan = 4;
        
        $row = $tblFormConsultaList->addRow();
        $cell = $row->addCell($this->pageNavigation);
        $cell->colspan = 4;
        */

        

        //$container = TVBox::pack( $this->form, $this->datagrid, $this->pageNavigation);
        //parent::add($container);        
        
        
        parent::add($tblShow);
        
        
        
    }
    
    /**
     * method onReload()
     * Load the datagrid with the database objects
     */
    function onReload($param = NULL)
    {
        print_r("teste");

        $this->datagrid->clear();
        
        try
        {
            
            // open a transaction with database 'consultorio'
            TTransaction::open('consultorio');
            
            // creates a repository for Paciente
            $repository = new TRepository('Consulta');
            $limit = 10;
            
            
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if( ! isset($param['order']) )
            {
                $param['order'] = 'cns_id';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);

            
/*
            if (TSession::getValue('PacienteList_filter_pts_id')) {
                $criteria->add(TSession::getValue('PacienteList_filter_pts_id')); // add the session filter
            }


            if (TSession::getValue('PacienteList_filter_pts_nome')) {
                $criteria->add(TSession::getValue('PacienteList_filter_pts_nome')); // add the session filter
            }


            if (TSession::getValue('PacienteList_filter_pts_cpf')) {
                $criteria->add(TSession::getValue('PacienteList_filter_pts_cpf')); // add the session filter
            }


            if (TSession::getValue('PacienteList_filter_pts_data_nascimento')) {
                $criteria->add(TSession::getValue('PacienteList_filter_pts_data_nascimento')); // add the session filter
            }

  */          
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            
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
    
    public static function onDataConsultaExit( $param )
    {
        new TToast(print_r($param['key'],true));
        //new TToast(print_r($param['cns_data_hora_chegada'],true));
    }
    
}

?>