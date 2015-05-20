<?php
use Adianti\Database\TRecord;
//namespace Endereco;

class EstadoFederativo extends TRecord
{
    const TABLENAME = 'estados_federativos';
    const PRIMARYKEY = 'efs_id';
    const IDPOLICY = 'serial';

    private $pais;

    
    public function set_pais(Pais $pais){
        $this->pais = $pais;
        $this->efs_pai_id = $pais->pai_id;
    }
    
    public function get_pais(){
        if ( empty($this->pais)){
            $this->pais = new Pais($this->efs_pai_id);
        }
        return $this->pais;
    }

}

?>