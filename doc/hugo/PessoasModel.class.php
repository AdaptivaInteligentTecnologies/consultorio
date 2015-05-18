<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PessoasModel
 *
 * @author hugo
 */
class PessoasModel extends TRecord {
    //put your code here
    const TABLENAME = 'sch_api_sgs_data.pessoas';
    const PRIMARYKEY= 'id';
    //const IDPOLICY =  'max'; // {max, serial}
    const IDPOLICY =  'serial'; // {max, serial} 
    
    
    private $pessoa;
    private $contatos;
    private $enderecos;
    
    public function __construct($id=NULL) {
        parent::__construct($id);     
    }
    
    
    /**
     * Method addContact
     * Add a Contact to the Pessoa
     * @param $object Instance of ContatosMOdel
     */
    public function addContatos(ContatosModel $object)
    {
        $this->contatos[] = $object;
    }
    
    /**
     * Method addEnderecos
     * Add a Endereco to the Pessoa
     * @param $object Instance of EnderecosPessoaModel
     */
    public function addEnderecos(EnderecosPessoaModel $object)
    {
        $this->enderecos[] = $object;
    }
    
    /**
     * Method getContacts
     * Return the Pessoa Contatos
     * @return Collection of Contact
     */
    public function getContatos()
    {
            // load the related Contatost objects
     
        return $this->contatos;
        
    }
    
    /**
     * Method getContacts
     * Return the Pessoa Enderecos
     * @return Collection of Contact
     */
    public function getEnderecos()
    {
        return $this->enderecos;
        
    }
    
    
     public function get_Contatos()
    {
            // load the related Contatost objects
     
        return $this->contatos;
        
    }
    
    /**
     * Method getContacts
     * Return the Pessoa Enderecos
     * @return Collection of Contact
     */
    public function get_Enderecos()
    {
        return $this->enderecos;
        
    }
    /**
     * Reset aggregates
     */
    public function clearPartsContatos()
    {
        $this->contatos = array();
        $this->enderecos = array();
    }
    
    /**
     * Method setPessoa
     * Instacia qual o tipo de pessoa se pessoa fisica ou juridica
     * Sample of usage: $customer->category = $object;
     * @param $object Instance of Category
     */
    public function setPessoa($tipo)
    {
        $this->pss_tipo = $tipo;
        
        if  (!$this->pessoa) {
            if ($tipo == 'F'){
                $this->pessoa = new PessoasFisicasModel();
            } 
        
            if ($tipo == 'J'){
                
              $this->pessoa = new PessoasJuridicasModel();            
            }
        }
        
    }
    
     /**
     * getIdPessoa
     * 
     * @returns Ultima id da tabela pessoa
     */
    
    public function getIdPessoa() {
        
        return $this->getLastID();
    }
    
    /**
     * Method get_pessoa
     * Sample of usage: $cliente->pessoa->attribute;
     * @returns Pessoa fisica ou juridica instance
     */
    public function get_pessoa()
    {
        // returns the associated object
        return $this->pessoa;
    }
    
   
    
    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    
    public function load($id)
    {
         
        $repository = new TRepository('PessoasFisicasModel');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('psf_pss_id', '=', $id));
        $results = $repository->load($criteria);        
        foreach ($results as $result) {
          $this->pessoa = $result;  
        }
        
                
        
          $repository = new TRepository('PessoasJuridicasModel');
          $criteria = new TCriteria;
          $criteria->add(new TFilter('psj_pss_id', '=', $id));
          $results = $repository->load($criteria);          
          foreach ($results as $result) {
              $this->pessoa = $result;             
            }
            
            // load the related Contatost objects
        $repository = new TRepository('ContatosModel');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('ctt_pss_id', '=', $id));
        $this->contatos = $repository->load($criteria);
        
       
             // load the related Enderecos objects
        $repository = new TRepository('EnderecosPessoaModel');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('epe_pss_id', '=', $id));
        $this->enderecos = $repository->load($criteria);
    
        return parent::load($id); 
        // load the object itself
       
    }
    
    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        
        // store the object itself
        
        parent::store();
        
        if ($this->pss_tipo == 'F'){
          $this->pessoa->psf_pss_id = $this->id;  
          
        } 
        
        if ($this->pss_tipo == 'J') {
           $this->pessoa->psj_pss_id = $this->id;    
        }  
        $this->pessoa->store();
        
       // $this->pessoa->store();
        
        
        
        // delete the related Contact objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('ctt_pss_id', '=', $this->id));
        $repository = new TRepository('ContatosModel');
        $repository->delete($criteria);
        // store the related Contact objects
        if ($this->contatos)
        {
            foreach ($this->contatos as $contato)
            {
                
                unset($contato->id);
                $contato->ctt_pss_id = $this->id;
                $contato->store();
            }
        }
        
        // delete the related Contact objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('epe_pss_id', '=', $this->id));
        $repository = new TRepository('EnderecosPessoaModel');
        $repository->delete($criteria);
        // store the related Contact objects
        if ($this->enderecos)
        {
            foreach ($this->enderecos as $endereco)
            {
                unset($endereco->id);
                $endereco->epe_pss_id = $this->id;
                $endereco->store();
            }
        }
    }
    
    
    public function delete($id = NULL)
    {
        // the related pessoa objects
        $id = isset($id) ? $id : $this->id;
        if ($this->pss_tipo == 'F'){
            $repository = new TRepository('PessoasFisicasModel');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('psf_pss_id', '=', $id));
            $repository->delete($criteria);
            
        } else {
            $repository = new TRepository('PessoasJuridicasModel');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('psj_pss_id', '=', $id));
            $repository->delete($criteria);
        }
        
        // delete the related Contact objects
        $id = isset($id) ? $id : $this->id;
        $repository = new TRepository('ContatosModel');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('ctt_pss_id', '=', $id));
        $repository->delete($criteria);
        
        // delete the related Enderecos objects
        $id = isset($id) ? $id : $this->id;
        $repository = new TRepository('EnderecosPessoaModel');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('epe_pss_id', '=', $id));
        $repository->delete($criteria);
        
        // delete the object itself
        parent::delete($id);
    }

}