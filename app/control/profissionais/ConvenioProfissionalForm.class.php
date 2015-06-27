<?php
/**
 * System_userForm Registration
 * @author  <your name here>
 */


class ConvenioProfissionalForm extends TPage
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
        $this->form = new TForm('form_convenio_Profissional');
        $this->form->class = 'tform';

        // creates the table container
        $table = new TTable;
        $table->style = 'width: 100%';
        
        $table->addRowSet( new TLabel(_t('Description')), '', '','' )->class = 'tformtitle';
        
        // add the table inside the form
        $this->form->add($table);
        

        // create the form fields
        $id                       = new TEntry('cps_id');
        $descricao                = new TEntry('cps_descricao');
        $cmsCodigoOperadora       = new TEntry('cps_codigo_operadora');
        $cmsCodigoRegistroAns     = new TEntry('cps_codigo_registro_ans');
        
        // define the sizes
        $id->setSize(100);
        $descricao->setSize(200);
        
        // outros
        $id->setEditable(false);
        
        // validations
        $descricao->addValidation(_t('Name'), new TRequiredValidator);
        
        // add a row for the field id
        $row=$table->addRow();
        $cell = $row->addCell(new TLabel('ID:'));
        $cell = $row->addCell($id);
        $cell->colspan = 2;
        
        // add a row for the field descricao
        $row=$table->addRow();
        $cell = $row->addCell(new TLabel('Descrição:'));
        $cell = $row->addCell($descricao);
        $cell->colspan = 2;
        
        // add a row for the field Número Registro ANS
        $row=$table->addRow();
        $cell = $row->addCell(new TLabel('Registro na ANS:'));
        $cell = $row->addCell($cmsCodigoRegistroAns);
        $cell->colspan = 2;
        
        // add a row for the field Número Registro ANS
        $row=$table->addRow();
        $cell = $row->addCell(new TLabel('Código de operadora:'));
        $cell = $row->addCell($cmsCodigoOperadora);
        $cell->colspan = 2;
        
        
                
        
//        $row=$table->addRow();
//        $cell = $row->addCell($frame_groups);
//        $cell->colspan = 2;
        
//        $cell = $row->addCell($frame_programs);
//        $cell->colspan = 2;

        // create an action button (save)
        $save_button=new TButton('save');
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('ico_save.png');
        
        // create an new button (edit with no parameters)
        $new_button=new TButton('new');
        $new_button->setAction(new TAction(array($this, 'onEdit')), _t('New'));
        $new_button->setImage('ico_new.png');
        
        $list_button=new TButton('list');
        $list_button->setAction(new TAction(array('ConvenioProfissionalList','onReload')), 'Voltar');
        $list_button->setImage('ico_datagrid.png');
        
        // define the form fields
        $this->form->setFields(array($id,$descricao,$cmsCodigoOperadora,$cmsCodigoRegistroAns,$save_button,$new_button,$list_button));
        
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
        $container->addRow()->addCell(new TXMLBreadCrumb('menu.xml', 'ConvenioProfissionalList'));
        $container->addRow()->addCell($this->form);

        // add the form to the page
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
            // open a transaction with database 'permission'
            TTransaction::open('consultorio');
            
            // get the form data into an active record System_user
            $object = $this->form->getData('ConvenioProfissional');
            
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
                $object = new ConvenioProfissional($key);
                
                
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