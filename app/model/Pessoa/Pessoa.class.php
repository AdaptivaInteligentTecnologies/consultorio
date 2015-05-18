<?php
//namespace Pessoa;


// depende de endereço e contato


use Adianti\Database\TRecord;
class Pessoa extends TRecord implements IPessoa
{
    const TABLENAME = 'pessoas';
    const PRIMARYKEY = 'pss_id';
    const IDPOLICY = 'serial';
    
    const tipoPessoaArray = array('F','f','J','j');
    
    private $contatos;
    private $enderecos;
    
    
    public function set_pss_tipo($tipo){
        
        if (in_array(self::tipoPessoaArray,$tipo)){
            $this->tipoPessoa = strtoupper($tipo);
        }
    }
    
    public function getTipoPessoa(){
        return $this->pss_tipo_pessoa;
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
    
    
}

?>