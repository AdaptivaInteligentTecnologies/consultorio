<?php
use Adianti\Widget\Container\TTable;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Wrapper\TDBCheckGroup;
use Adianti\Widget\Wrapper\TDBRadioGroup;
use Adianti\Widget\Form\TCheckGroup;
use Adianti\Widget\Form\TCombo;
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Form\TRadioButton;
/**
 * ConsultorioForm Registration
 * @author  <your name here>
 */
class ConsultorioForm extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new TQuickForm('form_Consultorio');
        $this->form->class = 'tform'; // CSS class
        $this->form->style = 'width: 100%';
        
        // define the form title
        $this->form->setFormTitle('Cadastro de consultório');
        


        // create the form fields
        $con_id                         = new TEntry('con_id');
        $con_id->setSize(100);
        $con_id->setEditable(FALSE);
        
        
        $con_emp_id                     = new TDBCombo('con_emp_id', 'consultorio', 'Empresa', 'emp_id', 'emp_nome_fantasia');
        
        
        $con_nome                       = new TEntry('con_nome');
        $con_nome->setSize(300);
        
        $con_ini_expediente             = new TEntry('con_ini_expediente');
        $con_ini_expediente->setMask('99:99');
        
        $con_fim_expediente             = new TEntry('con_fim_expediente');
        $con_fim_expediente->setMask('99:99');
        
        
        $itemsSimNao                    = array("S"=>"SIM","N"=>"NÃO");
        
        
        $con_fecha_para_almoco          = new TCombo('con_fecha_para_almoco');

        $con_fecha_para_almoco->addItems($itemsSimNao);
        
        $con_ini_almoco                 = new TEntry('con_ini_almoco');
        $con_ini_almoco->setMask('99:99');
        
        $con_fim_almoco                 = new TEntry('con_fim_almoco');
        $con_fim_almoco->setMask('99:99');
        
        $con_fun_segunda                = new  TCombo('con_fun_segunda');
        $con_fun_segunda->addItems($itemsSimNao);
        
        $con_fun_terca                  = new TCombo('con_fun_terca');
        $con_fun_terca->addItems($itemsSimNao);
        
        $con_fun_quarta                 = new TCombo('con_fun_quarta');
        $con_fun_quarta->addItems($itemsSimNao);
        
        $con_fun_quinta                 = new TCombo('con_fun_quinta');
        $con_fun_quinta->addItems($itemsSimNao);
        
        $con_fun_sexta                  = new TCombo('con_fun_sexta');
        $con_fun_sexta->addItems($itemsSimNao);
        
        $con_fun_sabado                = new TCombo('con_fun_sabado');
        $con_fun_sabado->addItems($itemsSimNao);
        
        $con_fun_domingo                = new TCombo('con_fun_domingo');
        $con_fun_domingo->addItems($itemsSimNao);
        
        

        
        //tabela para comportar dados gerais
        
        $dadosGerais = new TTable();
        $dadosGerais->style = 'width:100%';
       
        $dadosGerais->addRowSet( new TLabel('Cadastro de consultório'), '', '','' )->class = 'tformtitle';
        $dadosGerais->addRowSet( new TLabel('ID: '), $con_id,'','');
        $dadosGerais->addRowSet( new TLabel('Nome Consultório: '), $con_nome,'Empresa Vinculada: ',$con_emp_id);
        $dadosGerais->addRowSet( new TLabel('Início expediente: '),$con_ini_expediente, new TLabel('Fim expediente: '), $con_fim_expediente);

        $row = $dadosGerais->addRow();
        $cell = $row->addCell('DIAS DE FUNCIONAMENTO');
        $cell->style = 'text-align:center; background-color:silver';
        $cell->colspan = 4;
        
        $dadosGerais->addRowSet('Seungda: ',$con_fun_segunda, 'Terça:  ',$con_fun_terca);
        $dadosGerais->addRowSet('Quarta:  ',$con_fun_quarta , 'Quinta: ',$con_fun_quinta);
        $dadosGerais->addRowSet('Sexta:   ',$con_fun_sexta  , 'Sábado: ',$con_fun_sabado);
        $dadosGerais->addRowSet('Domingo: ',$con_fun_domingo, ''        ,'');
        
        $row = $dadosGerais->addRow();
        $cell = $row->addCell('ALMOÇO');
        $cell->style = 'text-align:center; background-color:silver';
        $cell->colspan = 4;
        
        $dadosGerais->addRowSet('Fecha para almoço: ',$con_fecha_para_almoco,'','');
        $dadosGerais->addRowSet('Início almoço: ',$con_ini_almoco,'Fim almoço: ',$con_fim_almoco);
        
        // create the form actions
        
        $this->form->add($dadosGerais);

        $this->form->setFields(
                                array(
                                        $con_id,$con_nome,$con_emp_id,$con_fecha_para_almoco,
                                        $con_ini_almoco,$con_fim_almoco,$con_ini_expediente,
                                        $con_fim_expediente,$con_fun_domingo,$con_fun_segunda,
                                        $con_fun_terca,$con_fun_quarta,$con_fun_quinta,
                                        $con_fun_sexta,$con_fun_sabado
                                    )
                                );
        
        
        // create the form actions
        $save_button = TButton::create('save', array($this, 'onSave'), _t('Save'), 'ico_save.png');
        $new_button  = TButton::create('new',  array($this, 'onEdit'), _t('New'),  'ico_new.png');
        $list_button = TButton::create('list', array('ConsultorioList','onReload'),_t('Back to the listing'),'ico_datagrid.png');
        
        $this->form->addField($save_button);
        $this->form->addField($new_button);
        $this->form->addField($list_button);
        
        $buttons_box = new THBox;
        $buttons_box->add($save_button);
        $buttons_box->add($new_button);
        $buttons_box->add($list_button);
        
        // add a row for the form action
        $row = $dadosGerais->addRow();
        $row->class = 'tformaction'; // CSS class
        $row->addCell($buttons_box)->colspan = 4;        
        
        // add the form to the page
        //parent::add($this->form);
        $container = new TTable;
        $container->style = 'width: 100%';
        $container->addRow()->addCell(new TXMLBreadCrumb('menu.xml', 'ConsultorioList'));
        $container->addRow()->addCell($this->form);
        //$container->addRow()->addCell($buttons_box)->class = 'tformaction'; 

        // add the form to the page
        parent::add($container);
    }
    
    public function onSave()
    {
        try {
            TTransaction::open('consultorio');
            
            $object = $this->form->getData('Consultorio');
            
            $this->form->validate();
            
            
            $object->store();

            $this->form->setData($object);
            
            TTransaction::close();
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));     

        }
        catch (Exception $e)
        {
            new TMessage('error', 'Erro ao gravar dados: '.$e->getMessage());
            TTransaction::rollback();
        }
        
    }
    
    function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                $key=$param['key'];  // get the parameter $key
                TTransaction::open('consultorio'); // open a transaction
                $object = new Consultorio($key); // instantiates the Active Record
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
    }
