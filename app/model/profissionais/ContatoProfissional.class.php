<?php
//namespace Endereco;
// depende de tipo de contato
use Adianti\Database\TRecord;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;

class ContatoProfissional extends TRecord
{
    const TABLENAME  = 'contatos_profissionais';
    const PRIMARYKEY = 'ctp_id';
    const IDPOLICY   = 'max';

    //private $tipoContato;
    private $tco_descricao;
   
    
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('ctp_pfs_id');
        parent::addAttribute('ctp_tco_id');
        parent::addAttribute('ctp_valor');
        parent::addAttribute('tco_descricao');
        parent::addAttribute('mf_tco_descricao');
        
        
    }
    
    
    
    public function get_tco_descricao()
    {
        TTransaction::open('consultorio');
        $tipoContato    = new TipoContato($this->ctp_tco_id);
        TTransaction::close();
        return $tipoContato->tco_descricao;
    }
    
        
    /*
    public function set_tipoContato(TipoContato $tipoContato){
        $this->tipoContato = $tipoContato;
        $this->ctp_tco_id = $tipoContato->tco_id;
        return $this;
    }

    public function get_tipoContato(){
        if ( empty($this->tipoContato)){
            $this->tipoContato = new TipoContato($this->ctp_tco_id);
        }
        return $this->tipoContato;
    }
    
    public function __toString(){
        return '';
    }
    */
    
    
}

?>