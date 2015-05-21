<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BancosForm
 *
 * @author teste
 */
class BancosForm extends TPage
{
    private $notebook;
    private $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
       
        
        // security check
        
        $this->notebook = new TNotebook;
        $this->notebook->setSize(600, 130);
        
        // creates a table
        $table = new TTable;
        $table1 = new TTable;
        $table2 = new TTable;
        
        $row = $table->addRow();
        $row->addCell($table1);
        $cell=$row->addCell($table2);
        $cell->valign='top';
        
        // creates the form
        $this->form = new TForm('form_bancoa');
        $this->notebook->appendPage(_t('Data'), $table);
        $this->form->add($this->notebook);
        
        $options = array('Y'=>_t('Yes'), 'N'=>_t('No'));
        // create the form fields
        $id          = new TEntry('id');
        $name        = new TEntry('bnc_nome'); 
        $codigo        = new TEntry('bnc_codigo');        
        $active      = new TCombo('bnc_ativo');
        

        $id->setEditable(FALSE);
        $active->addItems($options);

        // define the sizes
        $id->setSize(100);
        $name->setSize(200, 40);        
        $active->setSize(200);

        // add a row for the field id
        $row=$table1->addRow();
        $cell=$row->addCell(new TLabel('ID:'));
        $cell->width='100px';
        $row->addCell($id);

        // add a row for the field name
        $row=$table1->addRow();
        $row->addCell(new TLabel(_t('Name') . ': '));
        $row->addCell($name);
        
        // add a row for the field active
        $row=$table1->addRow();
        $row->addCell(new TLabel(_t('Active') . ': '));
        $row->addCell($active);

        // add a row for the field projects
     

        // create an action button (save)
        $save_button=new TButton('save');
        // define the button action
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('ico_save.png');
        
        $back_button=new TButton('back');
        $back_button->setAction(new TAction(array($this, 'onBack')), _t('Back'));
        $back_button->setImage('ico_back.png');
        

        // define wich are the form fields
        $this->form->setFields(array($id,$name,$active,$save_button,$back_button));

        $container = new TTable;
        $titulo = new TLabel('Cadastro de Bancos');
        $titulo->setFontSize(20);
        $panel = new TPanel(730,40);
        $panel->put($titulo, 360, 1);
        $container->addRow()->addCell($panel);
        $container->addRow()->addCell($this->form);
        $table_button = new TPanel(600,100);        
        $table_button->add($save_button);
        $table_button->add($back_button);
        //$row->addCell();
        //$row->addCell();
        
        $container->addRow()->addCell($table_button);
       // $container->->addCell($back_button);
        
        // add the form to the page
        parent::add($container);
        
    }
    
    
    function onBack(){
        try
        {
          TApplication::executeMethod('BancosList','onReload');  
        } 
        catch (Exception $e) // em caso d exceção
        {
            TSession::setValue('logged', FALSE);
            
            // exibe a mensagem gerada pela exceção
            new TMessage('error', '<b>Erro</b> ' . $e->getMessage());
            // desfaz todas alterações no banco de dados
            TTransaction::rollback();
        }
        
    }

    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    function onSave()
    {
        try
        {
            // open a transaction with database 
            TTransaction::open('sgs');
            
            // get the form data into an active record Member
            $object = $this->form->getData('Bancos');
            
            // fill the form with the active record data
            $this->form->setData($object);
            
            
            // form validation
            $this->form->validate();
            // stores the object
            $object->store();
            
            // close the transaction
            TTransaction::close();
            
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            TApplication::executeMethod('BancosList','onReload');  
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
                
                // open a transaction with database 
                TTransaction::open('sgs');
                
             // instantiates object Member
                $object = new Bancos($key);                
                
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


