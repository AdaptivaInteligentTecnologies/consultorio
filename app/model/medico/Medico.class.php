<?php

use Adianti\Widget\Dialog\TMessage;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;
//namespace model\medico;

class Medico extends TRecord{

    const TABLENAME  = 'medicos';
    const PRIMARYKEY = 'med_id';
    const IDPOLICY   = 'max';
    

    protected $contatos;
    //protected $convenios;
    //protected $especialidades;
    
    
   public function __construct($id=NULL)
   {
       parent::__construct($id);
       
       parent::addAttribute('med_id');
       parent::addAttribute('med_numero_crm');
       parent::addAttribute('med_uf_crm');
       parent::addAttribute('med_nome');
       parent::addAttribute('med_cnes');
       
   }
   
   public function addContato(ContatoMedico $obj)
   {
       $this->contatos[] = $obj;
   }
    
   public function getContatos()
   {
       return $this->contatos;
   }
   
   public function clearParts()
   {
       $this->contatos = array();
       //$this->convenios = array();
       //$this->especialidades = array();
       
   }
   
   function store()
   {
        parent::store();
            
        $criteria = new TCriteria;
        $criteria->add(new TFilter('ctm_med_id', '=', $this->cts_med_id));
        
        $repository = new TRepository('ContatoMedico');
        $repository->delete($criteria);

        if ($this->contatos)
        {
            foreach ($this->contatos as $contato)
            {
                print_r($contato->data);
                
                
                unset($contato->ctm_med_id);
                unset($contato->tco_descricao); // unset para validação do campo multifield que não existe na base de destino

                $contato->ctm_med_id = $this->med_id;
                $contato->ctm_tco_id = $contato->data['ctm_tco_id'];
                $contato->ctm_valor =  $contato->data['ctm_valor'];
                
                $contato->store();
            }
        }     
   
   }
   
   
   
   function load($id = NULL)
   {
       //$this->contatos = parent::loadComposite('ContatoMedico', 'ctm_med_id', $id);
      //echo "medico: {$this->med_id}<br />";
       
       $contatos_rep = new TRepository('ContatoMedico');
       $criteria = new TCriteria();
       $criteria->add(new TFilter('ctm_med_id','=',$id));
       $this->contatos = $contatos_rep->load($criteria);
       /*  
       if ($this->contatos)
       {
           foreach ($this->contatos as $contato)
           {
               $this->addContato($contato);
           }
       }
       */
   
        return parent::load($id);
       //$this->load($id);
   }
   
   
   function delete( $id = NULL)
   {
        parent::deleteComposite('ContatoMedico', 'ctm_med_id', $id);
        parent::delete($id);       
   }




/*
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