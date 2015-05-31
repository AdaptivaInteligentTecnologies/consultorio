<?php
//namespace Pessoa;


// depende de endereço e contato


use Adianti\Database\TRecord;
use Adianti\Database\TRepository;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Widget\Form\TFile;

class Pessoa extends TRecord
{

    const TABLENAME  = 'pessoas';
    const PRIMARYKEY = 'pss_id';
    const IDPOLICY   = 'max';
    
    const tipoPessoaArray = array('F','f','J','j');
    
    protected $contatos;
    protected $enderecos;
    
    
    
    
    public function __construct(){
        parent::__construct();
        parent::addAttribute('pss_tipo');
    }
    
    public function clearParts(){
        $this->contatos = array();
        $this->endereco = array();
    }    
    
    public function addContato(Contato $contato){
        $this->contatos[] = $contato;
    }
    
    public function getContatos(){
        return $this->contatos;
    }
    
    public function addEndereco(Endereco $endereco){
        $this->enderecos[] = $endereco;
    }
    
    public function getEnderecos(){
        return $this->enderecos;
    }
    
   
    public function load($id){
        
        
        // carrega contatos - COMPOSIÇÃO
        //$this->contatos = parent::loadComposite('Contato', 'cts_pss_id', $id);
        $contatos_rep = new TRepository('Contato');
        $criteriaContato = new TCriteria();
        $criteriaContato->add(new TFilter('cts_pss_id', '=', $id));
        $contatos = $contatos_rep->load($criteriaContato);
        if ($contatos){
            foreach ($contatos as $contato){
                $this->addContato($contato);
            }
        }
        
        // carrega endereços - AGREGAÇÃO
        //$criteriaPessoaTemEnderecos = new TCriteria();
        //$criteriaPessoaTemEnderecos->add(new TFilter('pte_pss_id', '=', $id));
        //$pessoaTemEnderecos = $enderecos_rep->load($criteriaPessoaTemEnderecos);
        //if ($pessoaTemEnderecos){
         // foreach ($pessoaTemEnderecos as $pessoaTemEndereco){
         //     $endereco = new Endereco($pessoaTemEnderecos->pte_id);
         //     $this->addEndereco($endereco);
         // }  
        
        // carrega endereços - COMPOSIÇÃO
        /*
        $enderecos_rep = new TRepository('Endereco');
        $criteriaEndereco = new TCriteria();
        $criteriaEndereco->add(new TFilter('eps_pss_id', '=', $id));
        $rEnderecos = $enderecos_rep->load($criteriaEndereco);
        if ($rEnderecos){
            foreach ($rEnderecos as $rEndereco){
                $this->addEndereco($rEndereco);
            }
        }
        $this->enderecos = parent::loadComposite('Endereco', 'eps_pss_id', $id);
        
        */
        
        return parent::load($id);
    }
    
    public function store(){
        parent::store();
        
        // grava contatos - COMPOSIÇÃO
        // store contatos
        $criteriaContatos = new TCriteria;
        $criteriaContatos->add(new TFilter('cts_pss_id', '=', $this->pss_id));
        $repositoryContatos = new TRepository('Contato');
        $repositoryContatos->delete($criteriaContatos);
        
        //print_r($this->med_id);
        if ($this->contatos)
        {
            foreach ($this->contatos as $contato)
            {
                $contao = new Contato;
                $contao->cts_pss_id = $this->pss_id;
                $contao->cts_tco_id = $contato->cts_tco_id;
                $contao->cts_valor =  $contato->cts_valor;
                $contao->store();
            }
        }
        
        
         // grava enderecos - COMPOSIÇÃO
/*
        $criteriaEndereco = new TCriteria();
        $criteriaEndereco->add(new TFilter('eps_pss_id','=',$this->pss_id));
        $enderecos_rep = new TRepository('Endereco');
        $enderecos_rep->delete($criteria);
        if ($this->enderecos){
            foreach ($this->enderecos as $endereco){
                $endereco->eps_pss_id = $this->pss_id;
                $endereco->store();
            }
        }
        //parent::saveComposite('Contato', 'cts_pss_id', $this->pss_id, $this->contatos);
        //parent::saveComposite('Endereco', 'eps_pss_id', $this->pss_id, $this->enderecos);
  */      
    }
    
    /*
    
    public function delete($id = NULL){
        
      //  $id = isset($id) ? $id : $this->{'pss_id'};
        
        // deleção de contatos por composição
        $criteriaContato = new TCriteria();
        $criteriaContato->add(new TFilter('cts_pss_id','=',$id));
        $contato_rep = new TRepository('Contato');
        $contato_rep->delete($criteriaContato);
        //parent::deleteComposite('Contato', 'cts_pss_id', $id);
        
        
        // deleção de endereços por composição
        $criteriaEndereco = new TCriteria();
        $criteriaEndereco->add(new TFilter('eps_pss_id','=',$id));
        $endereco_rep = new TRepository('Endereco');
        $endereco_rep->delete($criteriaEndereco);
        

        //parent::deleteComposite('Endereco', 'eps_pss_id', $id);
        
        //parent::delete($id);
    }
    */
}

?>