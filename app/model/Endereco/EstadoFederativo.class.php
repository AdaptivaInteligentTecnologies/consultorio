<?php
use Adianti\Database\TRecord;
//namespace Endereco;

class EstadoFederativo extends TRecord
{
    const TABLENAME = 'estados_federativos';
    const PRIMARYKEY = 'efs_id';
    const IDPOLICY = 'serial';

    private $pais;

    
    public function setPais(Pais $pais){
        $this->pais = $pais;
        $this->efs_pai_id = $pais->pai_id;
    }
    
    public function getPais(){
        if ( empty($this->pais)){
            $this->pais = new Pais($this->efs_pai_id);
        }
        return $this->pais;
    }

}

?>