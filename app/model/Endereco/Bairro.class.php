<?php
use Adianti\Database\TRecord;
//namespace Endereco;

class Bairro extends TRecord
{
    const TABLENAME = 'bairros';
    const PRIMARYKEY = 'brr_id';
    const IDPOLICY = 'serial';

    private $cidade;

    
    
    public function setCidade(Cidade $cidade){
        $this->cidade = $cidade;
        $this->brr_cde_id = $cidade->cde_id;
        return $this;
    }
    
    public function getCidade(){
        if ( empty($this->cidade)){
            $this->cidade = new Cidade($this->brr_cde_id);
        }
        return $this->cidade;
    }
    
    public function __toString(){
        return $this->brr_id.'-'.$this->brr_descricao;
    }

}

?>