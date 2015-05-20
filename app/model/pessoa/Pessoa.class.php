<?php
//namespace Pessoa;


// depende de endereço e contato


use Adianti\Database\TRecord;
use Adianti\Database\TRepository;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Widget\Form\TFile;

class Pessoa extends TRecord implements IPessoa
{

    const TABLENAME  = 'pessoas';
    const PRIMARYKEY = 'pss_id';
    const IDPOLICY   = 'serial';
    
    const tipoPessoaArray = array('F','f','J','j');
    
    private $contatos;
    private $enderecos;
    private $pessoa;
    
    
    
    public function __construct(){
        parent::__construct();
        
        //inicializar 
    }
    
    public function set_pss_tipo($param){
        
        if (in_array(self::tipoPessoaArray,$param)){
            $this->tipoPessoa = strtoupper($param);
        }
    }
    
    public function getTipoPessoa(){
        return $this->pss_tipo;
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
    
    public function setPessoa(IPessoa $pss){
        $this->pessoa = $pss;
        $this->eps_tipo = $pss::__TIPO_PESSOA__;
        return $this;
    }
    
    public function getPessoa(){
        return $this->pessoa;
    }
    
    public function load($id){
        
        
        // carrega contatos - COMPOSIÇÃO
        $this->contatos = parent::loadComposite('Contato', 'cts_pss_id', $id);
        /*
        $contatos_rep = new TRepository('Contato');
        $criteriaContato = new TCriteria();
        $criteriaContato->add(new TFilter('cts_pss_id', '=', $id));
        $rContatos = $contatos_rep->load($criteriaContato);
        if ($rContatos){
            foreach ($rContatos as $rContato){
                $this->addContato($rContato);
            }
        }
        */
        
        // carrega endereços - AGREGAÇÃO
/*        $enderecos_rep = new TRepository('PessoaTemEndereco');
        $criteriaPessoaTemEnderecos = new TCriteria();
        $criteriaPessoaTemEnderecos->add(new TFilter('pte_pss_id', '=', $id));
        $pessoaTemEnderecos = $enderecos_rep->load($criteriaPessoaTemEnderecos);
        if ($pessoaTemEnderecos){
          foreach ($pessoaTemEnderecos as $pessoaTemEndereco){
              $endereco = new Endereco($pessoaTemEnderecos->pte_id);
              $this->addEndereco($endereco);
          }  
        }
*/
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
        */
        $this->enderecos = parent::loadComposite('Endereco', 'eps_pss_id', $id);
        
        
        
        return parent::load($id);
    }
    
    public function store(){
        parent::store();
        
        // grava contatos - COMPOSIÇÃO
        /*
        $criteriaContato = new TCriteria();
        $criteriaContato->add(new TFilter('cts_pss_id','=',$this->pss_id));
        $contatos_rep = new TRepository('Contato');
        $contatos_rep->delete($criteria);
        if ($this->contatos){
            foreach ($this->contatos as $contato){
                $contato->cts_pss_id = $this->pss_id;
                // -- ATENÇÃO -- DEVO INCLUIR O TIPO DE CONTATO AQUI !!
                $contato->store();
            }
        }
        */
        
        
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
         */
        parent::saveComposite('Contato', 'cts_pss_id', $this->pss_id, $this->contatos);
        parent::saveComposite('Endereco', 'eps_pss_id', $this->pss_id, $this->enderecos);
        
    }
    
    public function delete($id = NULL){
        
        $id = isset($id) ? $id : $this->{'pss_id'};
        
        // deleção de contatos por composição
        /*
        $criteriaContato = new TCriteria();
        $criteriaContato->add(new TFilter('cts_pss_id','=',$id));
        $contato_rep = new TRepository('Contato');
        $contato_rep->delete($criteriaContato);
          */
        parent::deleteComposite('Contato', 'cts_pss_id', $id);
        
        
        // deleção de endereços por composição
/*
        $criteriaEndereco = new TCriteria();
        $criteriaEndereco->add(new TFilter('eps_pss_id','=',$id));
        $endereco_rep = new TRepository('Endereco');
        $endereco_rep->delete($criteriaEndereco);
        
*/
        parent::deleteComposite('Endereco', 'eps_pss_id', $id);
        
        parent::delete($id);
    }
    
}

?>