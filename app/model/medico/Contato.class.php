<?php
//namespace Endereco;
// depende de tipo de contato
use Adianti\Database\TRecord;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;

class Contato extends TRecord
{
    const TABLENAME  = 'contatos';
    const PRIMARYKEY = 'cts_id';
    const IDPOLICY   = 'max';

    protected $tipoContato;
    
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('cts_pss_id');
        parent::addAttribute('cts_tco_id');
        parent::addAttribute('cts_descricao');
        
    }
    
    public function addTipoContato(TipoContato $tipoContato)
    {
        $this->tipoContato = $tipoContato;
        $this->tipoContato->cts_tco_id = $tipoContato_id;
        
    }    
    
        
    /*
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
    */
    
    
}

?>