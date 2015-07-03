<?php
use Adianti\Widget\Wrapper\TDBSelect;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TQuickForm;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TTransaction;
use Adianti\Validator\TCNPJValidator;
use Adianti\Control\TAction;
use adianti\widget\util\TBuscaCEPCorreios;
use Adianti\Widget\Form\THidden;
use adianti\widget\dialog\TToast;

/**
 * EmpresaForm Registration
 * @author  <your name here>
 */
class EmpresaForm extends TPage
{
    protected $form; // form
    
    static $emp_cep_anterior;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_Empresa');
        $this->form->class = 'tform'; // CSS class
        $this->form->style = 'width: 100%';
        
        // add a table inside form
        $table = new TTable;
        $table-> width = '100%';
        $this->form->add($table);
        
        // add a row for the form title
        $row = $table->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Formulário Cadastro de Empresa') )->colspan = 2;
        


        // create the form fields
        $emp_id                         = new TEntry('emp_id');
        $emp_nome_fantasia              = new TEntry('emp_nome_fantasia');
        $emp_razao_social               = new TEntry('emp_razao_social');
        $emp_cnpj                       = new TEntry('emp_cnpj');
        $emp_inscricao_estadual         = new TEntry('emp_inscricao_estadual');
        $emp_inscricao_municipal        = new TEntry('emp_inscricao_municipal');

        $emp_cep                        = new TEntry('emp_cep');
        $actionCEP                      = new TAction(array($this,'onCEP'));
        $emp_cep->setExitAction($actionCEP);
        
        
        $emp_logradouro                 = new TEntry('emp_logradouro');
         
        $emp_numero                     = new TEntry('emp_numero');
        $emp_uf                         = new TEntry('emp_uf');
        $emp_cidade                     = new TEntry('emp_cidade');
        $emp_bairro                     = new TEntry('emp_bairro');

        // define the sizes
        $emp_id->setSize(100);
        $emp_id->setEditable(False);
        
        
        $emp_nome_fantasia->setSize(200);
        $emp_razao_social->setSize(200);
        $emp_cnpj->setSize(200);
        $emp_inscricao_estadual->setSize(200);
        $emp_inscricao_municipal->setSize(200);
        $emp_cep->setSize(200);
        $emp_logradouro->setSize(200);
        $emp_numero->setSize(200);
        
        // define masks
        $emp_cnpj->setMask('99.999.999/9999-99');
        $emp_cnpj->addValidation('CNPJ', new TCNPJValidator());
        
        
        $emp_cep->setMask('99999-999');
        
        // define ID names
        $emp_nome_fantasia->id  = 'empNomeFantasia';
        $emp_razao_social->id   = 'empRazaoSocial';
        $emp_logradouro->id     = 'empLogradouro';
        
        // define actions
        //$emp_efs_id->setChangeAction(new TAction(array($this,'onUFChange'),'onchangeUF'));
        


        // add one row for each form field
        $table->addRowSet( new TLabel('ID:'), $emp_id );
        $table->addRowSet( new TLabel('Nome Fantasia:'), $emp_nome_fantasia );
        $table->addRowSet( new TLabel('Razão Social:'), $emp_razao_social );
        $table->addRowSet( new TLabel('CNPJ:'), $emp_cnpj );
        $table->addRowSet( new TLabel('Inscrição Estadual:'), $emp_inscricao_estadual );
        $table->addRowSet( new TLabel('Inscrição Municipal:'), $emp_inscricao_municipal );
        $table->addRowSet( new TLabel('CEP:'), $emp_cep );
        $table->addRowSet( new TLabel('Logradouro:'), $emp_logradouro );
        $table->addRowSet( new TLabel('Bairro:'), $emp_bairro );
        $table->addRowSet( new TLabel('Cidade:'), $emp_cidade );
        $table->addRowSet( new TLabel('UF:'), $emp_uf );
        $table->addRowSet( new TLabel('Número:'), $emp_numero );
        

        $this->form->setFields(
                                array(
                                        $emp_id,$emp_nome_fantasia,$emp_razao_social,
                                        $emp_cnpj,$emp_inscricao_estadual,$emp_inscricao_municipal,
                                        $emp_cep,$emp_logradouro, $emp_numero,
                                        $emp_uf,$emp_cidade,$emp_bairro
                                    
                                    )
                                );
        

        // create the form actions
        $save_button = TButton::create('save', array($this, 'onSave'), _t('Save'), 'ico_save.png');
        $new_button  = TButton::create('new',  array($this, 'onEdit'), _t('New'),  'ico_new.png');
        $list_button = TButton::create('list', array('EmpresaList','onReload'),'Voltar','ico_datagrid.png');
        
        $this->form->addField($save_button);
        $this->form->addField($new_button);
        $this->form->addField($list_button);
        
        $buttons_box = new THBox;
        $buttons_box->add($save_button);
        $buttons_box->add($new_button);
        $buttons_box->add($list_button);
        
        // add a row for the form action
        $row = $table->addRow();
        $row->class = 'tformaction'; // CSS class
        $row->addCell($buttons_box)->colspan = 2;

        /*
        $script = new TElement('script');
        $script->type = 'text/javascript';
        $script->add('$("#empNomeFantasia").on("input", function(evt) {$(this).val(function (_, val) {return val.toUpperCase();});});');
        $script->add('$("#empRazaoSocial").on("input", function(evt) {$(this).val(function (_, val) {return val.toUpperCase();});});');
        $script->add('$("#empLogradouro").on("input", function(evt) {$(this).val(function (_, val) {return val.toUpperCase();});});');
        */
        //$script->add('$("#medNomeMae").on("input", function(evt) {$(this).val(function (_, val) {return val.toUpperCase();});});');
        //$script->add('$("#medUfCrm").on("input", function(evt) {$(this).val(function (_, val) {return val.toUpperCase();});});');
        //$script->add('$("#medCpf").iMask({type : "fixed", mask : "999.999.999-99"});');
        
        $container = new TTable;
        $container->style = 'width: 80%';
        $container->addRow()->addCell(new TXMLBreadCrumb('menu.xml', 'CadastroProfissionalList'));
        $container->addRow()->addCell($this->form);
        
        parent::add($container);        

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
            
            // get the form data into an active record Empresa
            $object = $this->form->getData('Empresa');
            $this->form->validate(); // form validation
            $object->store(); // stores the object
            $this->form->setData($object); // keep form data
            TTransaction::close(); // close the transaction
            
            // shows the success message
            //new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            new TToast('Registro salvo');
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
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
                $object = new Empresa($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form

                static::$emp_cep_anterior = $object->emp_cep;
                //echo static::$emp_cep_anterior;
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

    
    
    public static function onUFChange($param)
    {
        
        //new TMessage('info', print_r($param['emp_efs_id'],true));
/*
        try 
        {
            TTransaction::open('consultorio');

            $repository = new TRepository('EstadoFederativo');
            $criterio = new TCriteria();
            $criterio->add(new TFilter('cde_efs_id', '=', $param['emp_efs_id']));
            $collection = $repository->load(new TCriteria);
            // add the combo items
            $items = array();
            foreach ($collection as $object)
            {
                $items[$object->efs_id] = $object->efs_nome;
            }            
            TDBCombo::reload('form_Empresa', 'emp_brr_id', $items);
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error','Erro: '.$e->getMessage());
            TTransaction::rollback();
        }
        */
        
    }
    
    public static function onCEP($param){
        //if (trim($param['emp_cep_anterior']) != trim($param['emp_cep']) and (!empty(trim($param['emp_cep']))) )
        if ( !empty( trim( $param['emp_cep'] ) ) )
        {
            $data   = new TBuscaCEPCorreios($param['emp_cep']);
            $obj    = new StdClass;
            $obj->emp_logradouro = $data->getLogradouro();
            $obj->emp_cidade     = $data->getCidade();
            $obj->emp_bairro     = $data->getBairro();;
            $obj->emp_uf         = $data->getUf();;
            TForm::sendData('form_Empresa', $obj);
        }
        
        
    }    
    
}
