<?php
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Container\THBox;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Form\TSeekButton;
use Adianti\Widget\Form\TMultiField;
use Adianti\Widget\Container\TNotebook;
use Adianti\Widget\Form\THidden;
/**
 * System_userForm Registration
 * @author  Sebastião Carnegie
 */


class CadastroMedicoForm extends TPage
{
    protected $form; // form
   
    private $notebook;
    
    
    
    
    //private $especialidadesMedicas; // = array();
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TForm('form_cadastro_medico');
        $this->form->class = 'tform';

        // creates the table container
        $table = new TTable;
        $table->style = 'width: 100%';
        
        $table->addRowSet( new TLabel('Cadastro Médico'), '', '','' )->class = 'tformtitle';
        
        // add the table inside the form
        $this->form->add($table);
        
        
        
        

        // create the form fields
        $medId                              = new TEntry('med_id');
        $medNumeroCrm                       = new TEntry('med_numero_crm');
        $medUfCrm                           = new TEntry('med_uf_crm');
        $medNome                            = new TEntry('med_nome');
        $medCnes                            = new TEntry('med_cnes');
        
        //$emsIdH                             = new THidden('emsIdH');
        
        $especialidadesMedicasMultiField    = new TMultiField('fieldsEspecialidadesMedicas');
        
        $emsId              = new TDBSeekButton('emsId', 'consultorio', 'form_cadastro_medico', 'EspecialidadeMedica', 'ems_descricao', 'ems_id', 'ems_descricao');
        $emsDescricao       = new TEntry('emsDescricao');
        
        $especialidadesMedicasMultiField->setHeight(150);
        $especialidadesMedicasMultiField->setClass('EspecialidadeMedica');
        $especialidadesMedicasMultiField->addField('id', ' ID',  $emsId, 100, true);
        $especialidadesMedicasMultiField->addField('emsDescricao', 'Descrição' , $emsDescricao, 250,false);
        $especialidadesMedicasMultiField->setOrientation('horizontal');
        
        
        
        
        
        
        // define the sizes
        $medId->setSize(100);
        $medNumeroCrm->setSize(100);
        $medUfCrm->setSize(35);
        $medNome->setSize(300);
        $medCnes->setSize(100);
        $emsId->setSize(50);
        $emsDescricao->setSize(250);
        
        // outros
        $medId->setEditable(false);
        $emsDescricao->setEditable(false);
        // validations
        $medNome->addValidation('Nome', new TRequiredValidator);
        $medNumeroCrm->addValidation('Número CRM', new TRequiredValidator);
        $medUfCrm->addValidation('UF CRM', new TRequiredValidator);
        
        // add a row for the field id
        $lblNomeMedico = new TLabel('Nome: ');
        $lblNomeMedico->class = 'lbl-required-field';
        
        $blbNumeroCrm = new TLabel('Número do CRM: ');
        $blbNumeroCrm->class = 'lbl-required-field';
        
        $blbUfCrm = new TLabel('UF do CRM: ');
        $blbUfCrm->class = 'lbl-required-field';
        
        $table->addRowSet(new TLabel('ID:'), $medId);
        $table->addRowSet($lblNomeMedico, $medNome);
        $table->addRowSet($blbNumeroCrm, $medNumeroCrm);
        $table->addRowSet($blbUfCrm, $medUfCrm);
        $table->addRowSet(new TLabel('CNES:'), $medCnes);
        
        // create an action button (save)
        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('ico_save.png');
        
        // create an new button (edit with no parameters)
        $new_button=new TButton('new');
        $new_button->setAction(new TAction(array($this, 'onEdit')), _t('New'));
        $new_button->setImage('ico_new.png');
        

        $list_button=new TButton('list');
        $list_button->setAction(new TAction(array('CadastroMedicoList','onReload')), _t('Back to the listing'));
        $list_button->setImage('ico_datagrid.png');

        
        // define the form fields
        //$this->form->setFields(array($id,$descricao,$save_button,$new_button,$list_button));
        $this->form->setFields(array($medId,$medNome,$medNumeroCrm,$medUfCrm,$medCnes,$especialidadesMedicasMultiField,$save_button,$new_button,$list_button));
        

        $frameEspecialidadesMedicas = new TFrame;
        $frameEspecialidadesMedicas->oid = 'frame-especialidades-medicas';
        $frameEspecialidadesMedicas->setLegend('Especialidades Médicas');
        $frameEspecialidadesMedicas->add($especialidadesMedicasMultiField);
        
        $frameContatos = new TFrame;
        $frameContatos->oid = 'frame-convenios-medicas';
        $frameContatos->setLegend('Contatos');
        
        $frameConvenios = new TFrame;
        $frameConvenios->oid = 'frame-convenios-medicas';
        $frameConvenios->setLegend('Convênios');
        

        
        $this->notebook = new TNotebook('85%',300);
        $this->notebook->appendPage('Especialidades Médicas', $especialidadesMedicasMultiField);
        //$this->notebook->appendPage('Contatos', $null);
        //$this->notebook->appendPage('Convênios', $null);
        
//        $frames = new THBox();
//        $frames->add($frameEspecialidadesMedicas);
//        $frames->add($frameConvenios);
        
        $row=$table->addRow();
        $cell = $row->addCell( new TElement('hr') );
        $cell->colspan = 4;
        
        
        $row=$table->addRow();
        //$row->class = 'tformaction';
        //$cell = $row->addCell( $frames );
        $cell = $row->addCell( $this->notebook );
        $cell->colspan = 4;
        $cell->align = 'center';
        
        $cell->align ='center';
        
        
        
        $buttons = new THBox;
        $buttons->add($save_button);
        $buttons->add($new_button);
        $buttons->add($list_button);

        $row=$table->addRow();
        $row->class = 'tformaction';
        $cell = $row->addCell( $buttons );
        $cell->colspan = 4;
        
        
        
        
        $container = new TTable;
        $container->style = 'width: 80%';
        $container->addRow()->addCell(new TXMLBreadCrumb('menu.xml', 'CadastroMedicoList'));
        $container->addRow()->addCell($this->form);
        
        
        parent::add($container);
        
        
        // add the form to the page
        //parent::add($container);
    }

    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    function onSave()
    {
        try
        {
            // open a transaction with database 'permission'
            TTransaction::open('consultorio');
            
            // get the form data into an active record Medico
            $object = $this->form->getData('Medico');
            // form validation
            $this->form->validate();
            
            $object->store(); // stores the object
            
            // fill the form with the active record data
            $this->form->setData($object);
            
            // close the transaction
            TTransaction::close();
            
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            // reload the listing
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
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                // get the parameter $key
                $key=$param['key'];
                
                // open a transaction with database 'permission'
                TTransaction::open('consultorio');
                
                // instantiates object System_user
                $object = new Medico($key);
                
                
                // fill the form with the active record data
                $this->form->setData($object);
                
                // close the transaction
                TTransaction::close();
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}
?>