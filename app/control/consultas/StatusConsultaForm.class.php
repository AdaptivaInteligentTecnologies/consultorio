<?php
/**
 * StatusConsultaForm Registration
 * @author  <your name here>
 */
class StatusConsultaForm extends TPage
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
        $this->form = new TForm('form_StatusConsulta');
        $this->form->class = 'tform'; // CSS class
        $this->form->style = 'width: 100%';
        
        // add a table inside form
        $table = new TTable;
        $table-> width = '100%';
        $this->form->add($table);
        
        // add a row for the form title
        $row = $table->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Cadastro de status das consultas') )->colspan = 2;
        


        // create the form fields
        $scs_id                         = new TEntry('scs_id');
        $scs_descricao                  = new TEntry('scs_descricao');
        $scs_cor                        = new TColor('scs_cor');


        // define the sizes
        $scs_id->setSize(100);
        $scs_id->setEditable(FALSE);
        
        $scs_descricao->setSize(200);
        $scs_cor->setSize(100);



        // add one row for each form field
        $table->addRowSet( new TLabel('ID: '), $scs_id );
        $table->addRowSet( new TLabel('Descrição: '), $scs_descricao );
        $table->addRowSet( new TLabel('COR: '), $scs_cor );


        $this->form->setFields(array($scs_id,$scs_descricao,$scs_cor));


        // create the form actions
        $save_button = TButton::create('save', array($this, 'onSave'), _t('Save'), 'ico_save.png');
        $new_button  = TButton::create('new',  array($this, 'onEdit'), _t('New'),  'ico_new.png');
        $list_button = TButton::create('list', array('StatusConsultaList','onReload'),_t('Back to the listing'),'ico_datagrid.png');
        
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
        
        parent::add($this->form);
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
            
            // get the form data into an active record StatusConsulta
            $object = $this->form->getData('StatusConsulta');
            $this->form->validate(); // form validation
            $object->store(); // stores the object
            $this->form->setData($object); // keep form data
            TTransaction::close(); // close the transaction
            
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
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
                $object = new StatusConsulta($key); // instantiates the Active Record
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
