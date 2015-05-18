<?php
use Adianti\Database\TTransaction;
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
            
            $bairro = new Bairro(1);
            
            $this->lgr = new Logradouro();
            $this->lgr->lgr_descricao = 'Rua A, Residencial Solar dos Encantos';
            $this->lgr->lgr_cep = '65052735';
            $this->lgr->lgr_numero = '6';
            $this->lgr->setBairro($bairro);
            $this->lgr->store();
            echo $this->lgr->getBairro();

            TTransaction::close();
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));            
        }catch (Exception $e){
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();            
        }
    }

}

?>