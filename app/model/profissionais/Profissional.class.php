<?php

use Adianti\Widget\Dialog\TMessage;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;
use Adianti\Database\TTransaction;


//namespace model\Profissional;

class Profissional extends TRecord{

    const TABLENAME     = 'profissionais';
    const PRIMARYKEY    = 'pfs_id';
    const IDPOLICY      = 'max';
    const CACHECONTROL  = 'TAPCache';
        
    
/*
    protected $pessoa;
    protected $pssId;
 */   
    protected   $contatosprofissionais;
    protected   $conveniosprofissionais;
    protected   $especialidadesProfissionais;
    
    protected   $enderecos;
    private     $estadoFederativo;
    
    
    
   public function __construct($id=NULL)
   {
       parent::__construct($id);
       
       parent::addAttribute('pfs_id');
       parent::addAttribute('pfs_con_id');
       parent::addAttribute('pfs_css_id');
       parent::addAttribute('pfs_numero_conselho');
       parent::addAttribute('pfs_nome');
       parent::addAttribute('pfs_conselho_uf_id');
       parent::addAttribute('pfs_cnes');
       //parent::addAttribute('pfs_pss_id');
       //$this->pessoa = new PessoaFisica($id);
        
       
   }

/*
   public function get_pessoaProfissional()
   {
       return $this->pessoa;
   }
   
   public function setPessoa($pssId=NULL)
   {
       $this->pssId = $pssId;
       return $this;
   }
  */ 
   
   static public function getCount()
   {
            $repository = new TRepository('Profissional');
            return $repository->count();
   }

   static public function getProfissionais()
   {
          try 
       {
           TTransaction::open('consultorio');
           $criteria = new TCriteria;
           $criteria->add( new TFilter( 'pfs_nome', '<>', '""' ));
           $profissionais = Profissional::getObjects($criteria);
           $resultProfissional = array();
           foreach ($profissionais as $Profissional)
           {
               $resultProfissional[$Profissional->pfs_id]= $Profissional->pfs_nome;
           }
           return $resultProfissional;           
           TTransaction::close();
       }
       catch (Exception $e)
       {
            TTransaction::rollback();
            new TMessage('error','Erro ao tentar buscar Médicos: '.$e->getMessage());    
       }
   }
   
   public function clearParts(){

       $this->contatosprofissionais       = array();
       $this->endereco              = array();
       $this->conveniosprofissionais      = array();
       $this->especialidadesProfissionais = array();
       
   }
   
   public function addContato(ContatoProfissional $contato){
       $this->contatosprofissionais[] = $contato;
   }
   
   public function getContatos(){
       return $this->contatosprofissionais;
   }
   
   public function addEndereco(Endereco $endereco){
       $this->enderecos[] = $endereco;
   }
   
   public function getEnderecos(){
       return $this->enderecos;
   }
   
   public function addEspecialidadeProfissional(EspecialidadeProfissional $especialidade)
   {
       $this->especialidadesProfissionais[] = $especialidade;
   }
   
   public function getEspecialidadesProfissionais()
   {
       return $this->especialidadesProfissionais;
   }
   
   public function addConvenioProfissional(ConvenioProfissional $convenio)
   {
       $this->conveniosprofissionais[] = $convenio;
   }
   
   public function getConveniosProfissionais()
   {
       return $this->conveniosprofissionais;
   }
   
    
    
    public function load($id)
    {

        // carrega contatos profissionais - COMPOSIÇÃO
        $contatosprofissionais_rep = new TRepository('ContatoProfissional');
        $criteriaContato = new TCriteria();
        $criteriaContato->add(new TFilter('ctp_pfs_id', '=', $id));
        $contatosprofissionais = $contatosprofissionais_rep->load($criteriaContato);
        if ($contatosprofissionais){
            foreach ($contatosprofissionais as $contato){
                $this->addContato($contato);
            }
        }
        
        // carrega especialidades profissionais - AGREGAÇÃO
        $profissionaisTemEspecialidades_rep = new TRepository('ProfissionalTemEspecialidade');
        $criteriaEspecialidadeProfissional = new TCriteria();
        $criteriaEspecialidadeProfissional->add(new TFilter('mte_pfs_id', '=', $id));
        $profissionaisTemEspecialidades = $profissionaisTemEspecialidades_rep->load($criteriaEspecialidadeProfissional);
        if ($profissionaisTemEspecialidades){
            foreach ($profissionaisTemEspecialidades as $ProfissionalTemEspecialidade){
                $especialidadeProfissional = new EspecialidadeProfissional($ProfissionalTemEspecialidade->mte_ems_id);
                $this->addEspecialidadeProfissional($especialidadeProfissional);
            }
        }
        
        // carrega convênios profissionais - AGREGAÇÃO
        $profissionaisTemConvenios_rep = new TRepository('ProfissionalTemConvenio');
        $criteriaConvenioProfissional = new TCriteria();
        $criteriaConvenioProfissional->add(new TFilter('ptc_pfs_id', '=', $id));
        $profissionaisTemConvenios = $profissionaisTemConvenios_rep->load($criteriaConvenioProfissional);
        if ($profissionaisTemConvenios){
            foreach ($profissionaisTemConvenios as $ProfissionalTemConvenio){
                $convenioProfissional = new ConvenioProfissional($ProfissionalTemConvenio->ptc_cps_id);
                $this->addConvenioProfissional($convenioProfissional);
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
       $criteriaContatos->add(new TFilter('ctp_pfs_id', '=', $this->pfs_id));
       $repositoryContatos = new TRepository('ContatoProfissional');
       $repositoryContatos->delete($criteriaContatos);
       
       if ($this->contatosprofissionais)
       {
           foreach ($this->contatosprofissionais as $contato)
           {
               unset($contato->ctp_pfs_id);
               unset($contato->tco_descricao);
               $contato->ctp_pfs_id = $this->pfs_id;
               $contato->store();
           }
       }

       // grava especialidades médicas - AGREGAÇÃO 
       $criteriaespecialidadesProfissionais = new TCriteria;
       $criteriaespecialidadesProfissionais->add(new TFilter('mte_pfs_id', '=', $this->pfs_id));
       $repositoryespecialidadesProfissionais = new TRepository('ProfissionalTemEspecialidade');
       $repositoryespecialidadesProfissionais->delete($criteriaespecialidadesProfissionais);
       // store the related CustomerSkill objects
       if ($this->especialidadesProfissionais)
       {
           foreach ($this->especialidadesProfissionais as $especialidadeProfissional)
           {
               //unset($ProfissionalTemEspecialidade->mte_id);
               $ProfissionalTemEspecialidade = new ProfissionalTemEspecialidade();
               $ProfissionalTemEspecialidade->mte_ems_id = $especialidadeProfissional->ems_id;
               $ProfissionalTemEspecialidade->mte_pfs_id = $this->pfs_id;
               $ProfissionalTemEspecialidade->store();
           }
       }
       
       // grava convênios profissionais - AGREGAÇÃO
       $criteriaConveniosprofissionais = new TCriteria;
       $criteriaConveniosprofissionais->add(new TFilter('ptc_pfs_id', '=', $this->pfs_id));
       $repositoryConveniosprofissionais = new TRepository('ProfissionalTemConvenio');
       $repositoryConveniosprofissionais->delete($criteriaConveniosprofissionais);
       if ($this->conveniosprofissionais)
       {
           foreach ($this->conveniosprofissionais as $convenioProfissional)
           {
               //unset($ProfissionalTemEspecialidade->mte_id);
               $ProfissionalTemConvenio = new ProfissionalTemConvenio();
               $ProfissionalTemConvenio->ptc_cps_id = $convenioProfissional->cps_id;
               $ProfissionalTemConvenio->ptc_pfs_id = $this->pfs_id;
               $ProfissionalTemConvenio->store();
           }
       }
        

           
        
    }
    
    public function delete($id)
    {
        parent::deleteComposite('ContatoProfissional', 'ctp_pfs_id', $id);
        parent::deleteComposite('ProfissionalTemEspecialidade', 'mte_pfs_id', $id);
        parent::deleteComposite('ProfissionalTemConvenio', 'ptc_pfs_id', $id);
        
        // delete the object itself
        parent::delete($id);        
    }
   
    
    public function get_uf_conselho()
    {
        if (empty($this->estadoFederativo))
        {
            $this->estadoFederativo = new EstadoFederativo($this->pfs_conselho_uf_id);
        }    
        
        return $this->estadoFederativo->efs_sigla;
        //return ;
    }
    
    public function __toString(){
        return '';
    }
    
    
    
    
}

?>