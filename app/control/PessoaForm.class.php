<?php
//namespace control;

use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
class PessoaForm extends TPage
{
    public function __construct($id = NULL)
    {
        parent::__construct($id);
    }
    
    public function onSave()
    {
        
        try 
        {
            TTransaction::open('consultorio');
                
            $pessoa = new PessoaFisica();
            $pessoa->psf_nome = 'teste';
            $pessoa->psf_nome_mae = 'teste2';
            $pessoa->psf_rg = '12014693-2';
            $pessoa->psf_cpf = '70609039334';
            $pessoa->psf_data_nascimento = '03/08/1976';
            $pessoa->sexo = 'F';
            $pessoa->setEstadoCivil(new EstadoCivil(2));
            $pessoa->store();
            
            TTransaction::close();
        }catch (Exception $e)
        {
            new TMessage('error', 'Erro: '.$e->getMessage());
            TTransaction::rollback();
        }
        
    }    
    
}

?>