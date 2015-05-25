<?php

use Adianti\Widget\Dialog\TMessage;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;


//namespace model\medico;

class Medico extends TRecord{

    const TABLENAME  = 'medicos';
    const PRIMARYKEY = 'med_id';
    const IDPOLICY   = 'max';
    

    private $contatos;
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
   
   
   // adiciona contaos médicos
   public function addContato(ContatoMedico $obj)
   {
       $this->contatos[] = $obj;
   }
    
   // retorna contatos médicos
   public function getContatos()
   {
       return $this->contatos;
   }
   
   public function addConvenio(ConvenioMedico $obj)
   {
       //$this->convenios[] = $obj;
   }
   
   public function getConvenios()
   {
       //return $this->convenios;
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
            //print_r($this->contatos);
        // store contatos
        $criteriaContatos = new TCriteria;
        $criteriaContatos->add(new TFilter('ctm_med_id', '=', $this->med_id));
        $repositoryContatos = new TRepository('ContatoMedico');
        $repositoryContatos->delete($criteriaContatos);
        
       //print_r($this->med_id);
        if ($this->contatos)
        {
            
            foreach ($this->contatos as $contato)
            {
                //print_r($contato->ctm_valor);
                $contaoMedico = new ContatoMedico;
                $contaoMedico->ctm_med_id = $this->med_id;
                $contaoMedico->ctm_tco_id = $contato->ctm_tco_id;
                $contaoMedico->ctm_valor =  $contato->ctm_valor;
                $contaoMedico->store();
                /*
                unset($contato->ctm_med_id);
                unset($contato->tco_descricao); // unset para validação do campo multifield que não existe na base de destino
                $contato->ctm_med_id = $this->med_id;
                $contato->ctm_tco_id = $contato->data['ctm_tco_id'];
                $contato->ctm_valor =  $contato->data['ctm_valor'];
                //print_r($contato);
                $contato->store();
                */
                
            }
        }     
        
        // store convênios
        //parent::saveAggregate('MedicoTemConvenio', 'mtc_cms_id', 'mtc_med_id', $this->med_id, $this->convenios);
        
        
//        $criteriaConvenios = new TCriteria;
//        $criteriaConvenios->add(new TFilter('mtc_med_id', '=', $this->med_id));
//        $repositoryConvenios = new TRepository('MedicoTemConvenio');
//        $repositoryConvenios->delete($criteriaConvenios);
        /*
        if ($this->convenios)
        {
            foreach ($this->convenios as $convenio)
            {
                $medicoTemConvenio = new MedicoTemConvenio();
                $medicoTemConvenio->mtc_med_id = $convenio->$this->med_id;
                $medicoTemConvenio->mtc_cms_id = $convenio->data['cms_id'];
                $medicoTemConvenio->store();
            }
        }
        
        */
        
        //parent::saveAggregate('MedicoTemConvenio', 'mtc_cms_med_id', 'cms_cns_id', $this->med_id, $this->convenios);
   
   }
   
   
   
   function load($id = NULL)
   {
       
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