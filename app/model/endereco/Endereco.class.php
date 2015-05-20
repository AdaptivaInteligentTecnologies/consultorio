<?php

use Adianti\Database\TRecord;
//namespace Endereco;

class Endereco extends TRecord
{
    
    const TABLENAME  = 'enderecos_pessoas';
    const PRIMARYKEY = 'eps_id';
    const IDPOLICY   = 'serial';
    

//    private $logradouro;
    
    const tipoEndereco = array('R','r','C','c');
    const recebeCorrespondencia = array('S','s','N','n');

    /*
    public function setlogradouro(logradouro $logradouro){
        $this->logradouro = $logradouro;
        $this->eps_lgr_id = $logradouro->lgr_id;
        return $this;
    }
    
    
    public function getlogradouro(){
        if ( empty($this->logradouro)){
            $this->logradouro = new logradouro($this->eps_lgr_id);
        }
        return $this->logradouro;
    }
    */

    public function set_eps_tipo($param)
    {
        if (in_array(trim($param), tipoEndereco))
        {
            $this->data['eps_tipo'] = strtoupper($param);
        }
        else
        {
            echo 'Erro ao atribuir tipo de endereço: '.strtoupper($param);
        };
    }
    
    public function get_eps_tipo()
    {
        return strtoupper( $this->data['eps_tipo'] );
    }
    
    
    public function set_eps_correspondencia($param)
    {
        if (in_array(trim($param), recebeCorrespondencia))
        {
            $this->data['eps_correpondencia'] = strtoupper($param);
        }
        else
        {
            echo 'Erro ao atribuir recebe correspondência: '. strtoupper($param);
        }
     }
    
   public function get_eps_correspondencia()
   {
        return $this->data['eps_correspondencia'];
    }
    
    
    public function __toString()
    {
        return '';
    }    
}

?>