<?php
use Adianti\Database\TTransaction;
use Adianti\Database\TCriteria;
use Adianti\Widget\Dialog\TMessage;
//namespace Enderecos;

class Executa extends TPage
{

    private $lgr;
    
    function __construct(){
        parent::__construct();

            
    }
    
    function onSave(){
        try {
            TTransaction::open('consultorio');
            
            $convenio = new MedicoTemConvenio();
            $criterio = new TCriteria;
            $criterio->add(new TFilter('mtc_med_id','=',1));
            
            new TMessage('info', $criterio->dump());
            
            //$convenio->delete();
            
            
            TTransaction::close();
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));            
        }catch (Exception $e){
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();            
        }
    }

}

?>