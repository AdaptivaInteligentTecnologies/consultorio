<?php
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Container\THBox;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Form\TSeekButton;
use Adianti\Widget\Form\TMultiField;
use Adianti\Widget\Container\TNotebook;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Wrapper\TDBEntry;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Container\TTable;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;
use Adianti\Database\TTransaction;
use Adianti\Widget\Menu\TMenu;
/**
 * System_userForm Registration
 * @author  Sebastião Carnegie
 */


class CadastroMedicoForm extends TPage
{
    protected $form; // form
   
    private $notebook;
    private $contatosMultiField;
    private $conveniosMultiField;
    
    
    
    
    
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
        $medNome                            = new TEntry('med_nome','consultorio','Medico','med_nome');
        $medCnes                            = new TEntry('med_cnes');
        
                
        
        // grupo especialidades médicas        
        $emsId = new TDBSeekButton('ems_id', 'consultorio', 'form_cadastro_medico', 'EspecialidadeMedica', 'ems_descricao', 'fieldsEspecialidadesMedicas_ems_id', 'fieldsEspecialidadesMedicas_ems_descricao');
        $emsDescricao                      = new TEntry('ems_descricao');
        
        $especialidadesMedicasMultiField    = new TMultiField('fieldsEspecialidadesMedicas');
        $especialidadesMedicasMultiField->setHeight(150);
        $especialidadesMedicasMultiField->setClass('EspecialidadeMedica');
        $especialidadesMedicasMultiField->addField('ems_id', ' ID',  $emsId, 100, true);
        $especialidadesMedicasMultiField->addField('ems_descricao', 'Descrição' , $emsDescricao, 250,false);
        $especialidadesMedicasMultiField->setOrientation('horizontal');
        
        // grupo contatos
        $tcoId                              = new TDBSeekButton('ctm_tco_id', 'consultorio', 'form_cadastro_medico', 'TipoContato', 'tco_descricao', 'fieldsContatos_ctm_tco_id', 'fieldsContatos_tco_descricao');
        $tcoDescricao                       = new TEntry('tco_descricao');
        $ctoValor                           = new TEntry('ctm_valor');
        $this->contatosMultiField    = new TMultiField('fieldsContatos');
        $this->contatosMultiField->setHeight(150);
        $this->contatosMultiField->setClass('ContatoMedico');
        $this->contatosMultiField->addField('ctm_tco_id', ' ID',  $tcoId, 100, true);
        $this->contatosMultiField->addField('tco_descricao', 'Descrição' , $tcoDescricao, 250,true);
        $this->contatosMultiField->addField('ctm_valor', 'Valor' , $ctoValor, 250,true);
        $this->contatosMultiField->setOrientation('horizontal');
       
        // grupo convenios
        $cmsId                              = new TDBSeekButton('cms_id', 'consultorio', 'form_cadastro_medico', 'ConvenioMedico', 'cms_descricao', 'fieldsConvenios_cms_id', 'fieldsConvenios_cms_descricao');
        $cmsDescricao                       = new TEntry('cms_descricao');
        $this->conveniosMultiField          = new TMultiField('fieldsConvenios');
        $this->conveniosMultiField->setHeight(150);
        $this->conveniosMultiField->setClass('ConvenioMedico');
        $this->conveniosMultiField->addField('cms_id', ' ID',  $cmsId, 100, true);
        $this->conveniosMultiField->addField('cms_descricao', 'Descrição' , $cmsDescricao, 250,false);
        $this->conveniosMultiField->setOrientation('horizontal');
        
        
        
        
        // define the sizes
        $medId->setSize(100);

        $medNome->setSize(300);
        $medNome->setMaxLength(50);
        $medNome->ID='medNome';
        
        
        $medNumeroCrm->setSize(100);
        $medNumeroCrm->setMaxLength(20);
        $medNumeroCrm->setMask('9999999999999999999');
        
        
       
        $medUfCrm->setSize(35);
        $medUfCrm->setMask(2);
        $medUfCrm->setMask('aa');
        $medUfCrm->ID='medUfCrm';
        
        
        $medCnes->setSize(100);


        $emsId->setSize(50);
        $emsDescricao->setSize(250);
        
        $cmsId->setSize(50);
        $cmsDescricao->setSize(250);
        
        $tcoId          -> setSize(60);
        $tcoDescricao   -> setSize(200);
        $ctoValor       -> setSize(200);
        
        
        // outros
        $medId->setEditable(false);
        $emsDescricao->setEditable(false);
        $cmsDescricao->setEditable(false);
        $tcoDescricao->setEditable(false);
        
        
        
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
        
        $tableFields = new TTable();
        $tableFields->addRowSet(new TLabel('ID:'), $medId);
        $tableFields->addRowSet($lblNomeMedico, $medNome);
        $tableFields->addRowSet($blbNumeroCrm, $medNumeroCrm);
        $tableFields->addRowSet($blbUfCrm, $medUfCrm);
        $tableFields->addRowSet(new TLabel('CNES:'), $medCnes);
        
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


        
        $accordion = new TAccordion('accordion');
        $accordion->appendPage('Dados Gerais',$tableFields);
//        $accordion->appendPage('Especialidades Médicas',$especialidadesMedicasMultiField);
        $accordion->appendPage('Contatos',$this->contatosMultiField);
        $accordion->appendPage('Convênios',$this->conveniosMultiField);
        //$accordion->style = 'height:100%';
        //$accordion->style .= ';height:150px';
        
        $row=$table->addRow();
        $cell = $row->addCell( $accordion );
        $cell->colspan = 4;

        $this->form->setFields(array($medId,$medNome,$medNumeroCrm,$medUfCrm,$medCnes,$this->contatosMultiField,$this->conveniosMultiField,$save_button,$new_button,$list_button));
        
        
        $buttons = new THBox;
        $buttons->add($save_button);
        $buttons->add($new_button);
        $buttons->add($list_button);

        $row=$table->addRow();
        $row->class = 'tformaction';
        $cell = $row->addCell( $buttons );
        $cell->colspan = 4;
        
        
        $script = new TElement('script');
        $script->type = 'text/javascript';
        $script->add('$("#medNome").on("input", function(evt) {$(this).val(function (_, val) {return val.toUpperCase();});});');
        $script->add('$("#medUfCrm").on("input", function(evt) {$(this).val(function (_, val) {return val.toUpperCase();});});');
        
        
        
        $container = new TTable;
        $container->style = 'width: 80%';
        $container->addRow()->addCell(new TXMLBreadCrumb('menu.xml', 'CadastroMedicoList'));
        $container->addRow()->addCell($this->form);
        $container->addRow()->addCell($script);
        
        
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

            $object->clearParts();
            
            //salva contatos
            if( $object->fieldsContatos )
            {
                foreach( $object->fieldsContatos as $contato )
                {
                    $object->addContato ($contato);
                }
            }
            
            //salva convênios
            if( $object->fieldsConvenios )
            {
                
                foreach( $object->fieldsConvenios as $convenio )
                {
                    $object->addConvenio ($convenio);
                }
            }
            
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
                
                //$rep_convenios_medicos_view = new TRepository();

                $this->contatosMultiField->setValue($object->getContatos());
                $this->conveniosMultiField->setValue($object->getConvenios());
                
                
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