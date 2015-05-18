<?php

use Adianti\Database\TRecord;
//namespace Endereco;

class Endereco extends TRecord
{
    
    const TABLENAME = 'enderecos_pessoas';
    const PRIMARYKEY = 'eps_id';
    const IDPOLICY = 'serial';
    

    private $logradouro;
    
    const tipoEndereco = array('F','f','J','j');
    const recebeCorrespondencia = array('S','s','N','n');
    
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
    

    public function set_eps_tipo($param){
        if (in_array(trim($param), tipoEndereco)){
            $this->data['eps_tipo'] = $param;
        }else{
            echo "Erro ao atribuir tipo de endereço: {$param}";
        }
    }
    
    public function get_eps_tipo(){
        return $this->data['eps_tipo'];
    }
    
    
    public function set_eps_tipo($param){
        if (in_array(trim($param), tipoEndereco)){
            $this->data['eps_tipo'] = $param;
        }else{
            echo "Erro ao atribuir tipo de endereço: {$param}";
            }
        }
    
        public function get_eps_tipo(){
        return $this->data['eps_tipo'];
    }
    
    
    public function __toString(){
        return '';
    }    
}

?>