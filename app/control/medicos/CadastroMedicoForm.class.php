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
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TLabel;
use Adianti\Database\TFilter;
use Adianti\Control\TAction;
/**
 * System_userForm Registration
 * @author  Sebastião Carnegie
 */


class CadastroMedicoForm extends TPage
{
    protected $form; // form

    protected $contatosMedicosMultiField;
    protected $conveniosMedicosMultiField;
    protected $especialidadesMedicasMultiField;
    
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        
        $this->form                         = new TForm('form_cadastro_medico');
        $this->form->class = 'tform';

        // creates the table container
        $table                              = new TTable;
        $table->style = 'width: 100%';
        
        
        $table->addRowSet( new TLabel('Cadastro Médico'), '', '','' )->class = 'tformtitle';
        
        // add the table inside the form
        $this->form->add($table);

        // create the form fields
        
        // dados pessoais
        $medId                              = new TEntry('med_id');
        $medId->setSize(100);
        $medId->setEditable(false);
        

        $medNome                            = new TEntry('med_nome');           //nome do médico
        $medNome->setCompletion($this->loadNames());
        $medNome->addValidation('Nome do médico', new TRequiredValidator);
        $medNome->setSize(300);
        $medNome->setMaxLength(50);
        $medNome->ID='medNome';
        
        $medNumeroCrm                       = new TEntry('med_numero_crm');
        $medNumeroCrm->setSize(100);
        $medNumeroCrm->setMaxLength(20);
        $medNumeroCrm->setMask('9999999999999999999');
        $medNumeroCrm->addValidation('Número CRM', new TRequiredValidator);
        
        $medCrmUfId                         = new TDBCombo('med_crm_uf_id','consultorio','EstadoFederativo','efs_id','efs_sigla');
        $medCrmUfId->setSize(120);
        
        $medCnes                            = new TEntry('med_cnes');
        $medCnes->setSize(100);

        // add a row for the field id
        $lblNomeMedico                      = new TLabel('Nome: ');
        $lblNomeMedico->class = 'lbl-required-field';
        
        $blbNumeroCrm                       = new TLabel('Número do CRM: ');
        $blbNumeroCrm->class = 'lbl-required-field';
        
        $lblUfCrm                           = new TLabel('UF do CRM: ');
        $lblUfCrm->class = 'lbl-required-field';
        
        $lblSexo                            = new TLabel('Sexo: ');
        $lblSexo->class = 'lbl-required-field';
        
        $tableDadosGerais                   = new TTable();
        $tableDadosGerais->addRowSet(new TLabel('ID:'), $medId);
        $tableDadosGerais->addRowSet($lblNomeMedico, $medNome);
        
        
        $tableDadosGerais->addRowSet($blbNumeroCrm, $medNumeroCrm);
        $tableDadosGerais->addRowSet($lblUfCrm, $medCrmUfId);
        $tableDadosGerais->addRowSet(new TLabel('CNES:'), $medCnes);
        
       
        
        // contatos
        $tcoId                                              = new TDBSeekButton('ctm_tco_id', 'consultorio', 'form_cadastro_medico', 'TipoContato', 'tco_descricao', 'fieldsContatos_ctm_tco_id', 'fieldsContatos_tco_descricao');
        $tcoId->setSize(60);
        $tcoDescricao                                       = new TEntry('tco_descricao');
        $tcoDescricao->setEditable(FALSE);
        $ctoValor                                           = new TEntry('ctm_valor');
        $this->contatosMedicosMultiField                           = new TMultiField('fieldsContatos');
        $this->contatosMedicosMultiField->setClass('ContatoMedico');
        $this->contatosMedicosMultiField->setOrientation('horizontal');
        $this->contatosMedicosMultiField->addField('ctm_tco_id', ' ID',  $tcoId, 100, true);
        $this->contatosMedicosMultiField->addField('tco_descricao', 'Descrição' , $tcoDescricao, 250,true);
        $this->contatosMedicosMultiField->addField('ctm_valor', 'Valor' , $ctoValor, 250,true);
        //$this->contatosMedicosMultiField->setSize('100%','100%');
        
        
        // espcialidades médicas
        $emsId                                              = new TDBSeekButton('ems_id', 'consultorio', 'form_cadastro_medico', 'EspecialidadeMedica', 'ems_descricao', 'fieldsEspecialidadesMedicas_ems_id', 'fieldsEspecialidadesMedicas_ems_descricao');
        $emsId->setSize(60);
        $emsDescricao                                       = new TEntry('ems_descricao');
        $emsDescricao->setEditable(FALSE);
        $this->especialidadesMedicasMultiField              = new TMultiField('fieldsEspecialidadesMedicas');
        $this->especialidadesMedicasMultiField->setClass('EspecialidadeMedica');
        $this->especialidadesMedicasMultiField->setOrientation('horizontal');
        $this->especialidadesMedicasMultiField->addField('ems_id', ' ID',  $emsId, 100, true);
        $this->especialidadesMedicasMultiField->addField('ems_descricao', 'Descrição' , $emsDescricao, 250,true);
        //$this->especialidadesMedicasMultiField->setSize('100%','100%');
        

        // convênios médicos
        $cmsId                                              = new TDBSeekButton('cms_id', 'consultorio', 'form_cadastro_medico', 'ConvenioMedico', 'cms_descricao', 'fieldsConveniosMedicos_cms_id', 'fieldsConveniosMedicos_cms_descricao');
        $cmsId->setSize(60);
        $cmsDescricao                                       = new TEntry('cms_descricao');
        $cmsDescricao->setEditable(FALSE);
        $this->conveniosMedicosMultiField              = new TMultiField('fieldsConveniosMedicos');
        $this->conveniosMedicosMultiField->setClass('ConvenioMedico');
        $this->conveniosMedicosMultiField->setOrientation('horizontal');
        $this->conveniosMedicosMultiField->addField('cms_id', ' ID',  $cmsId, 100, true);
        $this->conveniosMedicosMultiField->addField('cms_descricao', 'Descrição' , $cmsDescricao, 250,true);
        //$this->especialidadesMedicasMultiField->setSize('100%','100%');
        
        
        
        
        // create an action button (save)
        $save_button                        = new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('ico_save.png');
        
        // create an new button (edit with no parameters)
        $new_button                         = new TButton('new');
        $new_button->setAction(new TAction(array($this, 'onEdit')), _t('New'));
        $new_button->setImage('ico_new.png');

        // create an new button (list with no parameters)
        $list_button                        = new TButton('list');
        $list_button->setAction(new TAction(array('CadastroMedicoList','onReload')), _t('Back to the listing'));
        $list_button->setImage('ico_datagrid.png');

        $accordion = new TAccordion('accordion');
        $accordion->appendPage('Dados Gerais',$tableDadosGerais);
        $accordion->appendPage('Contatos',$this->contatosMedicosMultiField);
        $accordion->appendPage('Especialidades Médicas', $this->especialidadesMedicasMultiField);
        $accordion->appendPage('Convênios',$this->conveniosMedicosMultiField);
        //$accordion->appendPage('Convênios',$this->conveniosMultiField);
        
        $row=$table->addRow();
        $cell = $row->addCell( $accordion );
        $cell->colspan = 4;

        $this->form->setFields(
        
            array(
                
                $medId,$medNome, $medNumeroCrm,
                $medCrmUfId,$medCnes,
                $this->contatosMedicosMultiField,
                $this->especialidadesMedicasMultiField,
                $this->conveniosMedicosMultiField,
                $save_button,$new_button,$list_button
                
            ));
        
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
        //$script->add('$("#medNomeMae").on("input", function(evt) {$(this).val(function (_, val) {return val.toUpperCase();});});');
        //$script->add('$("#medUfCrm").on("input", function(evt) {$(this).val(function (_, val) {return val.toUpperCase();});});');
        //$script->add('$("#medCpf").iMask({type : "fixed", mask : "999.999.999-99"});');
        
        $container = new TTable;
        $container->style = 'width: 80%';
        $container->addRow()->addCell(new TXMLBreadCrumb('menu.xml', 'CadastroMedicoList'));
        $container->addRow()->addCell($this->form);
        $container->addRow()->addCell($script);
        
        parent::add($container);
    }
    
    
    private function loadNames() // para pessoas físicas
    {
        try
        {
            TTransaction::open('consultorio');
            $nomes_rep                          = new TRepository('Medico');
            $nomes = $nomes_rep->load();
            $itemsNomes = array();
            foreach ($nomes as $nome)
            {
                $itemsNomes[] = $nome->med_nome;
            }
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error','Erro: '.$e->getMessage());
            $itemsNomes[]='ERRO AO CARREGAR NOMES';
        }
        return $itemsNomes;
    
    
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
            
            // fill the form with the active record data
            $this->form->setData($object);
            
            
            // salva contatos
            if( $object->fieldsContatos )
            {
                foreach( $object->fieldsContatos as $contato )
                {
                   //$object->pessoaMedico->pessoaFisica->addContato($contato); 
                   $object->addContato($contato); 
                }
            }

            // salva especialidades médicas
            
            if( $object->fieldsEspecialidadesMedicas )
            {
                foreach( $object->fieldsEspecialidadesMedicas as $especialidade )
                {
                    //$object->pessoaMedico->pessoaFisica->addContato($contato);
                    $object->addEspecialidadeMedica($especialidade);
                }
            }
            
            // salva convênios médicos
            
            if( $object->fieldsConveniosMedicos )
            {
                foreach( $object->fieldsConveniosMedicos as $convenio )
                {
                    //$object->pessoaMedico->pessoaFisica->addContato($contato);
                    $object->addConvenioMedico($convenio);
                }
            }
            
            
            $object->store(); // stores the object
            
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
                
                // carrega contatos no multifield
                if (!empty($object->getContatos()))
                { 
                    $this->contatosMedicosMultiField->setValue($object->getContatos());
                }
                
                // carrega especialidades médicas no multifield
                if (!empty($object->getEspecialidadesMedicas()))
                {
                    $this->especialidadesMedicasMultiField->setValue($object->getEspecialidadesMedicas());
                }

                // carrega convênios médicos no multifield
                if (!empty($object->getConveniosMedicos()))
                {
                    $this->conveniosMedicosMultiField->setValue($object->getConveniosMedicos());
                }
                
                
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