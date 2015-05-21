<?php
/**
 * SystemProgramForm Registration
 * @author  <your name here>
 */
class SystemProgramForm extends TStandardForm
{
    protected $form; // form
    protected $notebook;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
                
        // creates the notebook
        $this->notebook = new TNotebook;
        $this->notebook->setSize(500, 140);
                
        // creates the form
        $this->form = new TQuickForm('form_SystemProgram');
        $this->notebook->appendPage(_t('Program'), $this->form);
        
        // defines the database
        parent::setDatabase('permission');
        
        // defines the active record
        parent::setActiveRecord('SystemProgram');
        
        // create the form fields
        $id            = new TEntry('id');
        $name          = new TEntry('name');
        $controller    = new TEntry('controller');
        
        $id->setEditable(false);

        // add the fields
        $this->form->addQuickField('ID', $id,  50);
        $this->form->addQuickField(_t('Name') . ': ', $name,  200);
        $this->form->addQuickField(_t('Controller') . ': ', $controller,  200);

        // validations
        $name->addValidation(_t('Name'), new TRequiredValidator);
        $controller->addValidation(('Controller'), new TRequiredValidator);

        // add form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'ico_save.png');
        $this->form->addQuickAction(_t('New'), new TAction(array($this, 'onEdit')), 'ico_new.png');
        $this->form->addQuickAction(_t('Back to the listing'),new TAction(array('SystemProgramList','onReload')),'ico_datagrid.png');

        $container = new TTable();
        $container->addRow()->addCell(new TXMLBreadCrumb('menu.xml','SystemProgramList'));
        $container->addRow()->addCell($this->notebook);
        
        // add the form to the page
        parent::add($container);
    }
}
?>