<?php
use Adianti\Database\TRecord;
//namespace Endereco;

class Logradouro extends TRecord
{
    const TABLENAME = 'logradouros';
    const PRIMARYKEY = 'lgr_id';
    const IDPOLICY = 'serial';

    private $bairro;

    
    
    public function setBairro(Bairro $bairro){
        $this->bairro = $bairro;
        $this->lgr_brr_id = $bairro->brr_id;
        return $this;
    }
    
    public function getBairro(){
        if ( empty($this->bairro)){
            $this->bairro = new Bairro($this->lgr_brr_id);
        }
        return $this->bairro;
    }
    
    public function __toString(){
        return $this->lgr_id.'-'.$this->lgr_descricao;
    }

}

?>