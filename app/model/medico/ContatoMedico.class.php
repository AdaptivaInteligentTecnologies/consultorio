<?php
//namespace Endereco;
// depende de tipo de contato
use Adianti\Database\TRecord;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;

class ContatoMedico extends TRecord
{
    const TABLENAME  = 'contatos_medicos';
    const PRIMARYKEY = 'ctm_id';
    const IDPOLICY   = 'max';

    private $tipoContato;
    private $tco_descricao;
   
    
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        //parent::addAttribute('ctm_tco_id');
        //parent::addAttribute('ctm_valor');
        //parent::addAttribute('tco_descricao');
        //$this->tco_descricao = 'valor do contato';
        
    }
    
    public function set_tipoContato(TipoContato $tipoContato){
        $this->tipoContato = $tipoContato;
        $this->ctm_tco_id = $tipoContato->tco_id;
        return $this;
    }
    
    public function get_tco_descricao()
    {
        TTransaction::open('consultorio');
        $tipoContato = new TipoContato($this->ctm_tco_id);
        return $tipoContato->tco_descricao;
        TTransaction::close(); 
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