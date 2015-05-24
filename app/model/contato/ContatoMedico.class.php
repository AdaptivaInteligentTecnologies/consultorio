<?php
//namespace Endereco;
// depende de tipo de contato
use Adianti\Database\TRecord;

class ContatoMedico extends TRecord
{
    const TABLENAME  = 'contatos_medicos';
    const PRIMARYKEY = 'ctm_id';
    const IDPOLICY   = 'max';

    private $tipoContato;
    
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        //parent::addAttribute('cts_tco_id');
        //parent::addAttribute('cts_valor');

    }
    
    public function set_tipoContato(TipoContato $tipoContato){
        $this->tipoContato = $tipoContato;
        $this->ctm_tco_id = $tipoContato->tco_id;
        return $this;
    }
    
    public function get_tipoContato(){
        if ( empty($this->tipoContato)){
            $this->tipoContato = new TipoContato($this->ctm_tco_id);
        }
        return $this->tipoContato;
    }
    
    public function __toString(){
        return '';
    }
    
}

?>