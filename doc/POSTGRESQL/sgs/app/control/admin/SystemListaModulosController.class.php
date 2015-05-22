<?php
/**
 * System_userForm Registration
 * @author  <your name here>
 */
class SystemListaModulosController extends TPage
{
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
		new TMastHead('Módulos','Lista dos módulos cadastrados');
        
        $scroll = new TScroll('panelScroll');
        $scroll->setSize(400, 450);
        $objectTable = new TTable('tableField');
        $scroll->add($objectTable);
        
        for ($n=1;$n<=20;$n++){
        	
        	$objectEntry = new TEntry('field'.$n);

        	$row = $objectTable->addRow();
        	$row->addCell('Campo'.$n.': ');
        	$row->addCell($objectEntry);
        	
        	//$fieldsTable = 
        	
        }
        
/*
        $scroll = new TScroll;
        $scroll->setSize(290, 230);
        $scroll->add( $groups );
        $frame_groups->add( $scroll );
        $frame_programs->add( $multifield_programs );
*/        
        
        parent::add($scroll);
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
            TTransaction::open('permission');
            
            // get the form data into an active record System_user
            $object = $this->form->getData('SystemUser');
            
            // form validation
            $this->form->validate();
            
            $senha = $object->password;
            
            if( ! $object->id )
            {
                if( ! $object->password )
                    throw new Exception(TAdiantiCoreTranslator::translate('The field ^1 is required', _t('Password')));
            }
            
            if( $object->password )
            {
                if( $object->password != $object->repassword )
                    throw new Exception(_t('The passwords do not match'));
                
                $object->password = md5($object->password);
            }
            else
                unset($object->password);
            
            
            if( $object->groups )
            {
                foreach( $object->groups as $group )
                {
                    $object->addSystemUserGroup( new SystemGroup($group) );
                }
            }
            
            if( $object->programs )
            {
                foreach( $object->programs as $program )
                {
                    $object->addSystemUserProgram( $program );
                }
            }
            
            $object->store(); // stores the object
            
            $object->password = $senha;
            
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
                TTransaction::open('permission');
                
                // instantiates object System_user
                $object = new SystemUser($key);
                
                unset($object->password);
                
                $groups = array();
                
                if( $groups_db = $object->getSystemUserGroups() )
                {
                    foreach( $groups_db as $grup )
                    {
                        $groups[] = $grup->id;
                    }
                }
                
                $object->programs = $object->getSystemUserPrograms();
                
                $object->groups = $groups;
                
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