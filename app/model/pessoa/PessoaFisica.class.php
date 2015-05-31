<?php
use Adianti\Database\TRecord;
use Adianti\Database\TTransaction;
//namespace Pessoa;

class PessoaFisica extends TRecord
{
    const TABLENAME      = 'pessoas_fisicas';
    const PRIMARYKEY     = 'psf_id';
    const IDPOLICY       = 'max';
    
    protected $estadoCivil;
    protected $pessoa;

        
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('psf_pss_id');
        parent::addAttribute('psf_nome');
        parent::addAttribute('psf_nome_mae');
        parent::addAttribute('psf_rg');
        parent::addAttribute('psf_cpf');
        parent::addAttribute('psf_data_nascimento');
        parent::addAttribute('psf_sexo');
        parent::addAttribute('psf_estado_civil_id');
        
        $this->pessoa = new Pessoa($id);
        
        
    }
    
    public function setEstadoCivil(EstadoCivil $estadoCivil)
    {
        $this->estadoCivil = $estadoCivil;
        $this->psf_estado_civil_id = $estadoCivil->ecs_id;
    }
    
    public function getEstadoCivil()
    {
        if (empty($this->estadoCivil))
        {
            $this->estadoCivil = new EstadoCivil($this->psf_estado_civil_id);
        }
        return $this->estadoCivil;
    }
    
    public function set_cpf($cpf)
    {
            $this->psf_cpf = preg_replace( '#[^0-9]#', '', $cpf);
            return $this;
    }
    
    public function get_pessoaFisica()
    {
        return $this->pessoa;
    }
    
    
    public function store()
    {
        
        $this->pessoa->pss_tipo = 'F';
        $this->pessoa->store();
        $this->psf_pss_id = $this->pessoa->pss_id;
        parent::store();
        
    }
    
}

?>