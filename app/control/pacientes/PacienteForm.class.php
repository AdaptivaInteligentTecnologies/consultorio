<?php
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Form\TCombo;
use adianti\widget\util\TBuscaCEPCorreios;
use Adianti\Validator\TCPFValidator;
use Adianti\Control\TAction;
use Adianti\Widget\Dialog\TMessage;
use adianti\widget\dialog\TToast;
use Adianti\Widget\Form\TLabel;
use Adianti\Validator\TRequiredValidator;
/**
 * PacienteForm Registration
 * @author  <your name here>
 */

class PacienteForm extends TPage
{
    protected $form; // form

    
    protected $actVoltar;
    protected $list_button;
    protected $save_button;
    protected $new_button;
//    protected $incluirFilaAtendimento_button;
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct($id = NULL)
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TForm('form_Paciente');
        $this->form->class = 'tform'; // CSS class
        $this->form->style = 'width: 100%';
        
        // add a table inside form
        $table = new TTable;
        $table-> width = '100%';
        $this->form->add($table);
        
        // add a row for the form title
        $row = $table->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Cadastro de paciente') )->colspan = 4;
        

        // create the form fields
        $pts_id                         = new TEntry('pts_id');
        $pts_ecs_id                     = new TDBCombo('pts_ecs_id', 'consultorio', 'EstadoCivil', 'ecs_id', 'ecs_descricao');
        $pts_crs_id                     = new TDBCombo('pts_crs_id', 'consultorio', 'CorRaca', 'crs_id', 'crs_descricao');
        //$pts_crs_id->setDefaultOption('');
        
        $pts_nome                       = new TEntry('pts_nome');
        $pts_nome_mae                   = new TEntry('pts_nome_mae');
        $pts_cpf                        = new TEntry('pts_cpf');
        $pts_sexo                       = new TCombo('pts_sexo');
        $pts_data_nascimento            = new TDate('pts_data_nascimento');
        $pts_cep                        = new TEntry('pts_cep');
        $pts_logradouro                 = new TEntry('pts_logradouro');
        $pts_numero                     = new TEntry('pts_numero');
        $pts_uf                         = new TEntry('pts_uf');
        $pts_cidade                     = new TEntry('pts_cidade');
        $pts_bairro                     = new TEntry('pts_bairro');
        $pts_data_cadastro              = new TDate('pts_data_cadastro');
        $pts_usr_id                     = new TEntry('pts_usr_id');


        // define properties
        $pts_id->setSize(100);
        $pts_id->setEditable(False);

        $pts_ecs_id->setSize(200);
        $pts_crs_id->setSize(200);
        $pts_nome->setSize(300);
        $pts_nome->addValidation('Nome', new TRequiredValidator());
        
        $pts_nome_mae->setSize(300);
        $pts_nome_mae->addValidation('Nome da mãe', new TRequiredValidator());
        
        $pts_cpf->setSize(200);
        $pts_cpf->addValidation('CPF', new TCPFValidator());
        //$actionCpfExit = new TAction(array($this,'onCpfExit'));
        //$pts_cpf->setExitAction($actionCpfExit);
        
        $pts_cpf->setMask('999.999.999-99');
        
        
        
        $pts_sexo->setSize(200);
        $itemsSexo = array("M"=>"MASCULINO","F"=>"FEMININO");
        $pts_sexo->addItems($itemsSexo);
        
        
        $pts_data_nascimento->setSize(180);
        $pts_data_nascimento->setMask('dd/mm/yyyy');
        $pts_data_nascimento->addValidation('Data Nascimento', new TRequiredValidator());
        
        $pts_cep->setSize(200);
        $pts_cep->setMask('99999-999');
        $actionCEP  = new TAction(array($this,'onCEP'));
        $pts_cep->setExitAction($actionCEP);        
        
        $pts_logradouro->setSize(200);
        $pts_numero->setSize(200);
        $pts_uf->setSize(200);
        $pts_cidade->setSize(200);
        $pts_bairro->setSize(200);
        
        $pts_data_cadastro->setSize(100);
        $pts_data_cadastro->setMask('dd/mm/yyyy');
        $pts_data_cadastro->setEditable(FALSE);
        
        
        $pts_pne = new TCombo('pts_pne');
        $itemsPNE = array("S"=>"SIM","N"=>"NÃO");
        $pts_pne->addItems($itemsPNE);
        
        $pts_usr_id->setSize(100);

        $lblNome         = new TLabel('Nome:');
        $lblNome->class = 'lbl-required-field';
        
        $lblNomeMae      = new TLabel('Nome da mãe:');
        $lblNomeMae->class = 'lbl-required-field';
        
        $lblDataNascimento      = new TLabel('Data Nascimento:');
        $lblDataNascimento->class = 'lbl-required-field';
        
        $table->addRowSet( new TLabel('ID:')                , $pts_id               , new TLabel('Data do cadastro:')   , $pts_data_cadastro );
        $table->addRowSet( $lblNome                         , $pts_nome             , $lblNomeMae                       , $pts_nome_mae );
        $table->addRowSet( new TLabel('CPF:')               , $pts_cpf              , new TLabel('Sexo:')               , $pts_sexo );
        $table->addRowSet( $lblDataNascimento               , $pts_data_nascimento  , new TLabel('Estado Civil:')       , $pts_ecs_id );
        $table->addRowSet( new TLabel('Cor/Raça:')          , $pts_crs_id           , new TLabel('CEP:')                , $pts_cep );
        $table->addRowSet( new TLabel('Logradouro:')        , $pts_logradouro       , new TLabel('UF:')                 , $pts_uf ); 
        $table->addRowSet( new TLabel('Cidade:')            , $pts_cidade           , new TLabel('Bairro:')             , $pts_bairro);
        $table->addRowSet( new TLabel('Número:')            , $pts_numero           , new TLabel('P.N.E ?')             , $pts_pne );

        $this->form->setFields(array($pts_id,$pts_ecs_id,$pts_crs_id,$pts_nome,$pts_nome_mae,$pts_cpf,$pts_sexo,$pts_data_nascimento,$pts_cep,$pts_logradouro,$pts_numero,$pts_pne,
            $pts_uf,$pts_cidade,$pts_bairro,$pts_data_cadastro,$pts_usr_id));

        // create the form actions
        $actSave = new TAction(array($this,'onSave'));
        $this->save_button = new TButton('save');
        $this->save_button->setAction($actSave, 'Salvar');
        $this->save_button->setImage('ico_save.png');
        
        //$this->save_button = TButton::create('save', array($this, 'onSave'), _t('Save'), 'ico_save.png');
        
        $this->new_button  = TButton::create('new',  array($this, 'onEdit'), _t('New'),  'ico_new.png');
//        $this->queue_include_button  = TButton::create('queue',  array($this, 'onIncluirFilaAtendimento'),'Incluir na fila de atendimento',  'ico_plus.png');
        
        
        $this->list_button = new TButton('list_button');
        
        //$this->incluirFilaAtendimento_button  = new TButton('incluirFilaAtendimento');
        //::create('incluirFilaAtendimento_button',  array($this, 'onIncluirFilaAtendimento'), 'Salvar e incluir na fila de atendimento',  'ico_add.png');
        
        //$frm = $this->form->getData('Paciente');
        //new TToast(print_r($frm->pts_id,true));
        
        $this->actVoltar = new TAction(array('PacienteList','onReload'));

        /*
         *      $this->actVoltar = new TAction(array('AgendaPacienteForm','onReload'));
                $this->list_button->setAction($this->actVoltar, 'Voltar para agenda');

         */
        
        $this->list_button->setAction($this->actVoltar, 'Voltar');
        $this->list_button->setImage('ico_datagrid.png');
        
        //$list_button = TButton::create( 'list',$this->actVoltar,'Voltar','ico_datagrid.png');
        
        $this->form->addField($this->save_button);
        $this->form->addField($this->new_button);
        $this->form->addField($this->list_button);
        //$this->form->addField($incluirFilaAtendimento_button);
        
        
        $buttons_box = new THBox;
        $buttons_box->add($this->save_button);
        $buttons_box->add($this->new_button);
        $buttons_box->add($this->list_button);
        //$buttons_box->add($incluirFilaAtendimento_button);
        
        
        // add a row for the form action
        $row = $table->addRow();
        $row->class = 'tformaction'; // CSS class
        $row->addCell($buttons_box)->colspan = 4;
        
        parent::add($this->form);
        
    }

    
    function onIncluirFilaAtendimento()
    {
        
        /*
         * testar se existe id do agendamento
         * testar se a data e hora de agendamento condiz com a data e hora de hoje. Caso contrário emitir um dialog para reagendamento
         * testar se o código e o nome do paciente estão condizentes
         */
        
    }
    
    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    function onSave()
    {
        try
        {
            TTransaction::open('consultorio'); // open a transaction
            
            // get the form data into an active record Paciente
            $object = $this->form->getData('Paciente');

            if (empty($object->pts_data_cadastro))
            {
                $object->pts_data_cadastro = date("d/m/Y");
            }
            
            if (empty($object->pts_pne))
            {
                $object->pts_pne = 'N';
            }
            
            $this->form->validate(); // form validation

            $object->store(); // stores the object
            
            TTransaction::close(); // close the transaction
            
            $this->form->setData($object); // keep form data
            
            new TToast(TAdiantiCoreTranslator::translate('Record saved'),'success','Sucesso',2000);
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    
    
    function onInsert($param)
    {
        //print_r($param);
        //sendData($form_name, $object)
        try
        {
            if (!empty($param['aps_pts_id']))
            {
                //print_r($param['aps_pts_id']);
                $key=$param['aps_pts_id'];  // get the parameter $key
                TTransaction::open('consultorio'); // open a transaction        
                $object = new Paciente($key);
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                 $object = new Paciente();
                 $object->pts_nome             = $param['aps_nome_paciente'];
                 $object->pts_data_nascimento  = $param['aps_data_nascimento'];
                 $this->form->setData($object); // fill the form
            }
                
             
                //$this->save_button->style = 'display:none';
                $this->new_button->style = 'display:none';
                //$this->incluirFilaAtendimento_button->style = 'display:none';
                $this->actVoltar = new TAction(array('AgendaPacienteForm','onReload'));
                //$this->actVoltar = new TAction(array('AgendaPacienteForm','onFormAgenda'));
                $this->list_button->setAction($this->actVoltar, 'Voltar para agenda');
                
        }
        catch (Exception $e) // in case of exception
            {
                new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
                TTransaction::rollback(); // undo all pending operations
            }    
          
    }
    
    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                $key=$param['key'];  // get the parameter $key
                TTransaction::open('consultorio'); // open a transaction
                
                $object = new Paciente($key); // instantiates the Active Record
                
                $object->pts_data_nascimento    = date("d/m/Y",strtotime($object->pts_data_nascimento));
                $object->pts_data_cadastro      = date("d/m/Y",strtotime($object->pts_data_cadastro));
                                
                $this->form->setData($object); // fill the form
                
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
      static public function onCEP($param)
      {
        //if (trim($param['emp_cep_anterior']) != trim($param['emp_cep']) and (!empty(trim($param['emp_cep']))) )
        if ( !empty( trim( $param['pts_cep'] ) ) )
        {
            $data   = new TBuscaCEPCorreios($param['pts_cep']);
            $obj    = new StdClass;
            $obj->pts_logradouro = $data->getLogradouro();
            $obj->pts_cidade     = $data->getCidade();
            $obj->pts_bairro     = $data->getBairro();;
            $obj->pts_uf         = $data->getUf();;
            TForm::sendData('form_Paciente', $obj);
        }
     }

}