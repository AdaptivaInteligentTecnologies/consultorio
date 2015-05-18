<?php
//namespace Endereco;
// depende de tipo de contato
use Adianti\Database\TRecord;

class Contato extends TRecord
{
    const TABLENAME  = 'contatos';
    const PRIMARYKEY = 'cts_id';
    const IDPOLICY   = 'serial';

    private $tipoContato;
    
    
    
    public function setTipoContato(TipoContato $tipoContato){
        $this->tipoContato = $tipoContato;
        $this->cts_tco_id = $tipoContato->tco_id;
        return $this;
    }
    
    public function getTipoContato(){
        if ( empty($this->tipoContato)){
            $this->cidade = new TipoContato($this->cts_tco_id);
        }
        return $this->tipoContato;
    }
    
    public function __toString(){
        return '';
    }
    
}

?>