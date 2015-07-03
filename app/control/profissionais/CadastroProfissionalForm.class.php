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
use model\profissionais\Conselho;
use adianti\widget\dialog\TToast;
/**
 * System_userForm Registration
 * @author  Sebastião Carnegie
 */


class CadastroProfissionalForm extends TPage
{
    protected $form; // form

    protected $contatosProfissionaisMultiField;
    protected $conveniosProfissionaisMultiField;
    protected $especialidadesProfissionaisMultiField;
    protected $agendaProfissional;
    
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        
        $this->form                       = new TForm('form_cadastro_Profissional');
        $this->form->class          = 'tform';

        // creates the table container
        $table                                  = new TTable;
        $table->style                     = 'width: 100%';
        
        
        $table->addRowSet( new TLabel('Cadastro Profissional'), '', '','' )->class = 'tformtitle';
        
        // add the table inside the form
        $this->form->add($table);

        // create the form fields
        
        // dados pessoais
        $pfsId                              = new TEntry('pfs_id');
        $pfsId->setSize(100);
        $pfsId->setEditable(false);

        $pfsNome                            = new TEntry('pfs_nome');           //nome do médico
        $pfsNome->setCompletion($this->loadNames());
        $pfsNome->addValidation('Nome do profissional', new TRequiredValidator);
        $pfsNome->setSize(300);
        $pfsNome->setMaxLength(50);
        $pfsNome->ID='profissionalNome';
        
        $pfsCss                    = new TDBCombo('pfs_css_id','consultorio','Conselho','css_id','css_descricao');
        $pfsCss->setSize(120);
        
        
        $pfsNumeroConselho                  = new TEntry('pfs_numero_conselho');
        $pfsNumeroConselho->setSize(100);
        $pfsNumeroConselho->setMaxLength(20);
        $pfsNumeroConselho->setMask('9999999999999999999');
        $pfsNumeroConselho->addValidation('Número Conselho', new TRequiredValidator);
        
        $pfsConselhoUfId                         = new TDBCombo('pfs_conselho_uf_id','consultorio','EstadoFederativo','efs_id','efs_sigla');
        $pfsConselhoUfId->setSize(120);
        
        $pfsCnes                            = new TEntry('pfs_cnes');
        $pfsCnes->setSize(100);

        $pfsConId                         = new TDBCombo('pfs_con_id','consultorio','Consultorio','con_id','con_nome');
        $pfsConId->setSize(300);
        
        
        
        
        // add a row for the field id
        $lblNomeProfissional                      = new TLabel('Nome: ');
        $lblNomeProfissional->class = 'lbl-required-field';
        
        $lblCss                       = new TLabel('Nome Conselho: ');
        $lblCss->class = 'lbl-required-field';
        
        $lblNumeroConselho                       = new TLabel('Nr. Conselho: ');
        $lblNumeroConselho->class = 'lbl-required-field';
        
        $lblUfConselho                           = new TLabel('UF Conselho: ');
        $lblUfConselho->class = 'lbl-required-field';

        $lblConsultorio                           = new TLabel('Consultório: ');
        $lblConsultorio->class = 'lbl-required-field';
        

        
        $lblSexo                            = new TLabel('Sexo: ');
        $lblSexo->class = 'lbl-required-field';
        
        $tableDadosGerais                   = new TTable();
        $tableDadosGerais->addRowSet(new TLabel('ID:'), $pfsId);
        $tableDadosGerais->addRowSet($lblNomeProfissional, $pfsNome);
        
        
        $tableDadosGerais->addRowSet($lblCss, $pfsCss);
        $tableDadosGerais->addRowSet($lblNumeroConselho, $pfsNumeroConselho);
        $tableDadosGerais->addRowSet($lblUfConselho, $pfsConselhoUfId);
        $tableDadosGerais->addRowSet(new TLabel('CNES:'), $pfsCnes);
        $tableDadosGerais->addRowSet($lblConsultorio, $pfsConId);
        
       
        
        // contatos
        $tcoId                                              = new TDBSeekButton('ctp_tco_id', 'consultorio', 'form_cadastro_Profissional', 'TipoContato', 'tco_descricao', 'fieldsContatos_ctp_tco_id', 'fieldsContatos_tco_descricao');
        $tcoId->setSize(60);
        $tcoDescricao                                       = new TEntry('tco_descricao');
        $tcoDescricao->setEditable(FALSE);
        $ctoValor                                           = new TEntry('ctp_valor');
        $this->contatosProfissionaisMultiField                           = new TMultiField('fieldsContatos');
        $this->contatosProfissionaisMultiField->setClass('ContatoProfissional');
        $this->contatosProfissionaisMultiField->setOrientation('horizontal');
        $this->contatosProfissionaisMultiField->addField('ctp_tco_id', ' ID',  $tcoId, 100, true);
        $this->contatosProfissionaisMultiField->addField('tco_descricao', 'Descrição' , $tcoDescricao, 250,true);
        $this->contatosProfissionaisMultiField->addField('ctp_valor', 'Valor' , $ctoValor, 250,true);
        //$this->contatosProfissionaisMultiField->setSize('100%','100%');
        
        
        // espcialidades médicas
        $emsId                                              = new TDBSeekButton('ems_id', 'consultorio', 'form_cadastro_Profissional', 'EspecialidadeProfissional', 'ems_descricao', 'fieldsEspecialidadesProfissionais_ems_id', 'fieldsEspecialidadesProfissionais_ems_descricao');
        $emsId->setSize(60);
        $emsDescricao                                       = new TEntry('ems_descricao');
        $emsDescricao->setEditable(FALSE);
        $this->especialidadesProfissionaisMultiField              = new TMultiField('fieldsEspecialidadesProfissionais');
        $this->especialidadesProfissionaisMultiField->setClass('EspecialidadeProfissional');
        $this->especialidadesProfissionaisMultiField->setOrientation('horizontal');
        $this->especialidadesProfissionaisMultiField->addField('ems_id', ' ID',  $emsId, 100, true);
        $this->especialidadesProfissionaisMultiField->addField('ems_descricao', 'Descrição' , $emsDescricao, 250,true);
        //$this->especialidadesProfissionaisMultiField->setSize('100%','100%');
        

        // convênios profissionais
        $cpsId                                              = new TDBSeekButton('cps_id', 'consultorio', 'form_cadastro_Profissional', 'ConvenioProfissional', 'cps_descricao', 'fieldsConveniosProfissionals_cps_id', 'fieldsConveniosProfissionals_cps_descricao');
        $cpsId->setSize(60);
        $cpsDescricao                                       = new TEntry('cps_descricao');
        $cpsDescricao->setEditable(FALSE);
        $this->conveniosProfissionaisMultiField              = new TMultiField('fieldsConveniosProfissionals');
        $this->conveniosProfissionaisMultiField->setClass('ConvenioProfissional');
        $this->conveniosProfissionaisMultiField->setOrientation('horizontal');
        $this->conveniosProfissionaisMultiField->addField('cps_id', ' ID',  $cpsId, 100, true);
        $this->conveniosProfissionaisMultiField->addField('cps_descricao', 'Descrição' , $cpsDescricao, 250,true);
        //$this->especialidadesProfissionaisMultiField->setSize('100%','100%');
        
        
        
        
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
        $list_button->setAction(new TAction(array('CadastroProfissionalList','onReload')), 'Voltar');
        $list_button->setImage('ico_datagrid.png');

        $accordion = new TAccordion('accordion');
        $accordion->appendPage('Dados Gerais',$tableDadosGerais);
        $accordion->appendPage('Contatos',$this->contatosProfissionaisMultiField);
        $accordion->appendPage('Especialidades Médicas', $this->especialidadesProfissionaisMultiField);
        $accordion->appendPage('Convênios',$this->conveniosProfissionaisMultiField);
        $accordion->appendPage('Agenda',$this->agendaProfissional);
        //$accordion->appendPage('Convênios',$this->conveniosMultiField);
        
        $row=$table->addRow();
        $cell = $row->addCell( $accordion );
        $cell->colspan = 4;

        $this->form->setFields(
        
            array(
                
                $pfsId,$pfsNome, $pfsCss,$pfsNumeroConselho,
                $pfsConselhoUfId,$pfsCnes,$pfsConId,
                $this->contatosProfissionaisMultiField,
                $this->especialidadesProfissionaisMultiField,
                $this->conveniosProfissionaisMultiField,
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
        $script->add('$("#pfsNome").on("input", function(evt) {$(this).val(function (_, val) {return val.toUpperCase();});});');
        //$script->add('$("#pfsNomeMae").on("input", function(evt) {$(this).val(function (_, val) {return val.toUpperCase();});});');
        //$script->add('$("#pfsUfConselho").on("input", function(evt) {$(this).val(function (_, val) {return val.toUpperCase();});});');
        //$script->add('$("#pfsCpf").iMask({type : "fixed", mask : "999.999.999-99"});');
        
        $container = new TTable;
        $container->style = 'width: 80%';
        $container->addRow()->addCell(new TXMLBreadCrumb('menu.xml', 'CadastroProfissionalList'));
        $container->addRow()->addCell($this->form);
        $container->addRow()->addCell($script);
        
        parent::add($container);
    }
    
    
    private function loadNames() // para pessoas físicas
    {
        try
        {
            TTransaction::open('consultorio');
            $nomes_rep                          = new TRepository('Profissional');
            $nomes = $nomes_rep->load();
            $itemsNomes = array();
            foreach ($nomes as $nome)
            {
                $itemsNomes[] = $nome->pfs_nome;
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

            // get the form data into an active record Profissional
            $object = $this->form->getData('Profissional');
            
            // form validation
            $this->form->validate();
            
            // fill the form with the active record data
            $this->form->setData($object);
            
            
            // salva contatos
            if( $object->fieldsContatos )
            {
                foreach( $object->fieldsContatos as $contato )
                {
                   //$object->pessoaProfissional->pessoaFisica->addContato($contato); 
                   $object->addContato($contato); 
                }
            }

            // salva especialidades profissionais
            
            if( $object->fieldsEspecialidadesProfissionais )
            {
                foreach( $object->fieldsEspecialidadesProfissionais as $especialidade )
                {
                    $object->addEspecialidadeProfissional($especialidade);
                }
            }
            
            // salva convênios profissionais
            
            if( $object->fieldsConveniosProfissionals )
            {
                foreach( $object->fieldsConveniosProfissionals as $convenio )
                {
                    $object->addConvenioProfissional($convenio);
                }
            }
            
            
            $object->store(); // stores the object
            
            // close the transaction
            TTransaction::close();
            
            // shows the success message
            
            new TToast(TAdiantiCoreTranslator::translate('Record saved'));
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
                
                $object = new Profissional($key);
                
                // carrega contatos no multifield
                if (!empty($object->getContatos()))
                { 
                    $this->contatosProfissionaisMultiField->setValue($object->getContatos());
                }
                
                // carrega especialidades médicas no multifield
                if (!empty($object->getEspecialidadesProfissionais()))
                {
                    $this->especialidadesProfissionaisMultiField->setValue($object->getEspecialidadesProfissionais());
                }

                // carrega convênios médicos no multifield
                if (!empty($object->getConveniosProfissionais()))
                {
                    $this->conveniosProfissionaisMultiField->setValue($object->getConveniosProfissionais());
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