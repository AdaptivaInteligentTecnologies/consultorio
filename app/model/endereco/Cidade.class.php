<?php
use Adianti\Database\TRecord;
//namespace Endereco;

class Cidade extends TRecord
{
    const TABLENAME = 'cidades';
    const PRIMARYKEY = 'cde_id';
    const IDPOLICY = 'serial';

    private $estadoFederativo;

    
    
    public function setEstadoFederativo(EstadoFederativo $estadoFederativo){
        $this->estadoFederativo = $estadoFederativo;
        $this->cde_efs_id = $estadoFederativo->efs_id;
        return $this;
    }
    
    public function getEstadoFederativo(){
        if ( empty($this->estadoFederativo)){
            $this->estadoFederativo = new Pais($this->cde_efs_id);
        }
        return $this->estadoFederativo;
    }
    
    public function __toString(){
        return $this->cde_id.'-'.$this->cde_descricao;
    }
    
    

}

?>