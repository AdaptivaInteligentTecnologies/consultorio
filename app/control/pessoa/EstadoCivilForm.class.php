<?php
//namespace control\pessoa;

use Adianti\Control\TPage;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Container\TTable;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Database\TTransaction;
use adianti\widget\dialog\TToast;
class EstadoCivilForm extends TPage
{

    protected $form;
    
    public function __construct()
    {
        parent::__construct();
        
        // cria o formulário
        $this->form = new TForm('form_estado_civil');
        $this->form->class = 'tform';
        
        // cria a tabela container
        $tableDados = new TTable();
        $tableDados->style = 'width: 100%';
        
        // adiciona linha de cabeçalho a tabela
        $tableDados->addRowSet(new TLabel('Cadastro de Estados Civis'),'')->class='tformtitle';
        
        // insere a tabela container dentro do form
        $this->form->add($tableDados);
        
        // cria campo id
        $ecsId = new TEntry('ecs_id');

        // cria campo descrição
        $ecsDescricao = new TEntry('ecs_descricao');
        $ecsDescricao->id = 'ecsDescricao';
        
        
        $ecsId->setEditable(FALSE);
        $ecsId->setSize(100);
        
        $ecsDescricao->setSize(200);
        $ecsDescricao->addValidation('Descrição do estado civil', new TRequiredValidator());
        
        $tableDados->addRowSet(new TLabel('ID: '),$ecsId);
        $tableDados->addRowSet(new TLabel('Descrição: '),$ecsDescricao);

        
        // cria ação salvar (save)
        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('ico_save.png');
        
        // create an new button (edit with no parameters)
        $new_button=new TButton('new');
        $new_button->setAction(new TAction(array($this, 'onEdit')), _t('New'));
        $new_button->setImage('ico_new.png');
        
        
        $list_button=new TButton('list');
        $list_button->setAction(new TAction(array('EstadoCivilList','onReload')), 'Voltar');
        $list_button->setImage('ico_datagrid.png');

        $this->form->setFields(array($ecsId,$ecsDescricao,$save_button,$new_button,$list_button));
        
        
        
        $buttons = new THBox;
        $buttons->add($save_button);
        $buttons->add($new_button);
        $buttons->add($list_button);
        
        $row=$tableDados->addRow();
        $row->class = 'tformaction';
        $cell = $row->addCell( $buttons );
        $cell->colspan = 2;
        
        
        $script = new TElement('script');
        $script->type = 'text/javascript';
        $script->add('$("#ecsDescricao").on("input", function(evt) {$(this).val(function (_, val) {return val.toUpperCase();});});');
        
        $container = new TTable;
        $container->style = 'width: 100%';
        $container->addRow()->addCell(new TXMLBreadCrumb('menu.xml', 'EstadoCivilList'));
        $container->addRow()->addCell($this->form);
        $container->addRow()->addCell($script);        
        
        parent::add($container);
        
    }
    
    public function onSave()
    {
        try 
        {
            // abre a transação
            TTransaction::open('consultorio');
            
            // armazena dados do formulário no objeto
            $object = $this->form->getData('EstadoCivil');
            
            // valida informações do formulário
            $this->form->validate();
            
            // salva os dados do objeto
            $object->store();
            
            // depois de salvo envia os dados de volta ao formulário
            $this->form->setData($object);
            
            // encerra a transação
            TTransaction::close();
            
            // mostra a mensagem de sucesso
            //new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            new TToast('Estado civil salvo com sucesso!');
            
        }catch (Exception $e) // em caso de erro
        {
            new TMessage('error', 'Erro: '.$e->getMessage()); // mostra a mensagem de erro
            TTransaction::rollback(); // desfaz todas as alterações em base de dados
        }
        
    }
    
    public function onEdit($param)
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
                $object = new EstadoCivil($key);
        
        
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