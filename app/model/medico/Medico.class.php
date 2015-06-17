<?php

use Adianti\Widget\Dialog\TMessage;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;
use Adianti\Database\TTransaction;


//namespace model\medico;

class Medico extends TRecord{

    const TABLENAME  = 'medicos';
    const PRIMARYKEY = 'med_id';
    const IDPOLICY   = 'max';
    
/*
    protected $pessoa;
    protected $pssId;
 */   
    protected   $contatosMedicos;
    protected   $conveniosMedicos;
    protected   $especialidadesMedicas;
    
    protected   $enderecos;
    private     $estadoFederativo;
    
    
    
   public function __construct($id=NULL)
   {
       parent::__construct($id);
       
       parent::addAttribute('med_id');
       parent::addAttribute('med_nome');
       parent::addAttribute('med_numero_crm');
       parent::addAttribute('med_crm_uf_id');
       parent::addAttribute('med_cnes');
       //parent::addAttribute('med_pss_id');
       //$this->pessoa = new PessoaFisica($id);
        
       
   }

/*
   public function get_pessoaMedico()
   {
       return $this->pessoa;
   }
   
   public function setPessoa($pssId=NULL)
   {
       $this->pssId = $pssId;
       return $this;
   }
  */ 

   static public function getMedicos()
   {
       try 
       {
           TTransaction::open('consultorio');
           
           $criteria = new TCriteria;
           $criteria->add( new TFilter( 'med_nome', '<>', '""' ));
           $medicos = Medico::getObjects($criteria);
           $resultMedico = array();
           foreach ($medicos as $medico)
           {
               $resultMedico[$medico->med_id]= $medico->med_nome;
           }
           
           return $resultMedico;           
           
           TTransaction::close();
       }
       catch (Exception $e)
       {
            TTransaction::rollback();
            new TMessage('error','Erro ao tentar buscar Médicos: '.$e->getMessage());    
       }
   }
   
   public function clearParts(){

       $this->contatosMedicos       = array();
       $this->endereco              = array();
       $this->conveniosMedicos      = array();
       $this->especialidadesMedicas = array();
       
   }
   
   public function addContato(ContatoMedico $contato){
       $this->contatosMedicos[] = $contato;
   }
   
   public function getContatos(){
       return $this->contatosMedicos;
   }
   
   public function addEndereco(Endereco $endereco){
       $this->enderecos[] = $endereco;
   }
   
   public function getEnderecos(){
       return $this->enderecos;
   }
   
   public function addEspecialidadeMedica(EspecialidadeMedica $especialidade)
   {
       $this->especialidadesMedicas[] = $especialidade;
   }
   
   public function getEspecialidadesMedicas()
   {
       return $this->especialidadesMedicas;
   }
   
   public function addConvenioMedico(ConvenioMedico $convenio)
   {
       $this->conveniosMedicos[] = $convenio;
   }
   
   public function getConveniosMedicos()
   {
       return $this->conveniosMedicos;
   }
   
    
    
    public function load($id)
    {

        // carrega contatos médicos - COMPOSIÇÃO
        $contatosMedicos_rep = new TRepository('ContatoMedico');
        $criteriaContato = new TCriteria();
        $criteriaContato->add(new TFilter('ctm_med_id', '=', $id));
        $contatosMedicos = $contatosMedicos_rep->load($criteriaContato);
        if ($contatosMedicos){
            foreach ($contatosMedicos as $contato){
                $this->addContato($contato);
            }
        }
        
        // carrega especialidades médicas - AGREGAÇÃO
        $medicosTemEspecialidades_rep = new TRepository('MedicoTemEspecialidade');
        $criteriaEspecialidadeMedica = new TCriteria();
        $criteriaEspecialidadeMedica->add(new TFilter('mte_med_id', '=', $id));
        $medicosTemEspecialidades = $medicosTemEspecialidades_rep->load($criteriaEspecialidadeMedica);
        if ($medicosTemEspecialidades){
            foreach ($medicosTemEspecialidades as $medicoTemEspecialidade){
                $especialidadeMedica = new EspecialidadeMedica($medicoTemEspecialidade->mte_ems_id);
                $this->addEspecialidadeMedica($especialidadeMedica);
            }
        }
        
        // carrega convênios médicos - AGREGAÇÃO
        $medicosTemConvenios_rep = new TRepository('MedicoTemConvenio');
        $criteriaConvenioMedico = new TCriteria();
        $criteriaConvenioMedico->add(new TFilter('mtc_med_id', '=', $id));
        $medicosTemConvenios = $medicosTemConvenios_rep->load($criteriaConvenioMedico);
        if ($medicosTemConvenios){
            foreach ($medicosTemConvenios as $medicoTemConvenio){
                $convenioMedico = new ConvenioMedico($medicoTemConvenio->mtc_cms_id);
                $this->addConvenioMedico($convenioMedico);
            }
        }
        
        
        return parent::load($id);
    }
    
    
    
    
    //gravar dados
    public function store()
    {

        parent::store();
       
       // grava contatos - COMPOSIÇÃO
       $criteriaContatos = new TCriteria;
       $criteriaContatos->add(new TFilter('ctm_med_id', '=', $this->med_id));
       $repositoryContatos = new TRepository('ContatoMedico');
       $repositoryContatos->delete($criteriaContatos);
       
       if ($this->contatosMedicos)
       {
           foreach ($this->contatosMedicos as $contato)
           {
               unset($contato->ctm_med_id);
               unset($contato->tco_descricao);
               $contato->ctm_med_id = $this->med_id;
               $contato->store();
           }
       }

       // grava especialidades médicas - AGREGAÇÃO 
       $criteriaEspecialidadesMedicas = new TCriteria;
       $criteriaEspecialidadesMedicas->add(new TFilter('mte_med_id', '=', $this->med_id));
       $repositoryEspecialidadesMedicas = new TRepository('MedicoTemEspecialidade');
       $repositoryEspecialidadesMedicas->delete($criteriaEspecialidadesMedicas);
       // store the related CustomerSkill objects
       if ($this->especialidadesMedicas)
       {
           foreach ($this->especialidadesMedicas as $especialidadeMedica)
           {
               //unset($medicoTemEspecialidade->mte_id);
               $medicoTemEspecialidade = new MedicoTemEspecialidade();
               $medicoTemEspecialidade->mte_ems_id = $especialidadeMedica->ems_id;
               $medicoTemEspecialidade->mte_med_id = $this->med_id;
               $medicoTemEspecialidade->store();
           }
       }
       
       // grava convênios médicos - AGREGAÇÃO
       $criteriaConveniosMedicos = new TCriteria;
       $criteriaConveniosMedicos->add(new TFilter('mtc_med_id', '=', $this->med_id));
       $repositoryConveniosMedicos = new TRepository('MedicoTemConvenio');
       $repositoryConveniosMedicos->delete($criteriaConveniosMedicos);
       if ($this->conveniosMedicos)
       {
           foreach ($this->conveniosMedicos as $convenioMedico)
           {
               //unset($medicoTemEspecialidade->mte_id);
               $medicoTemConvenio = new MedicoTemConvenio();
               $medicoTemConvenio->mtc_cms_id = $convenioMedico->cms_id;
               $medicoTemConvenio->mtc_med_id = $this->med_id;
               $medicoTemConvenio->store();
           }
       }
        

           
        
    }
    
    public function delete($id)
    {
        parent::deleteComposite('ContatoMedico', 'ctm_med_id', $id);
        parent::deleteComposite('MedicoTemEspecialidade', 'mte_med_id', $id);
        parent::deleteComposite('MedicoTemConvenio', 'mtc_med_id', $id);
        
        // delete the object itself
        parent::delete($id);        
    }
   
    
    public function get_uf_crm()
    {
        if (empty($this->estadoFederativo))
        {
            $this->estadoFederativo = new EstadoFederativo($this->med_crm_uf_id);
        }    
        
        return $this->estadoFederativo->efs_sigla;
        //return ;
    }
    
    public function __toString(){
        return '';
    }
    
    
    
    
}

?>