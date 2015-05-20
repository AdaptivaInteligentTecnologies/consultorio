<?php

//namespace model\medico;

class Medico extends TRecord{

    const TABLENAME  = 'medicos';
    const PRIMARYKEY = 'med_id';
    const IDPOLICY   = 'serial';
    
    
    
    //private $pessoa;
    
    
    /*
    public function set_pessoa(Pessoa $pss){
        $this->pessoa = $pss;
        $this->med_pss_id = $pss->pss_id;
        return $this;
    }
    
    public function get_pessoa(){
        if ( empty($this->pessoa)){
            $this->pessoa = new PessoaFisica($this->med_pss_id);
        }
        return $this->pessoa;
    }
    
    public function store(){
        $this->pessoa->store();
        parent::store();
        
    }
    
    */
    
    
    public function __toString(){
        return '';
    }
    
    
    
    
}

?>