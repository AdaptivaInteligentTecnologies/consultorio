<?php
/**
 * Empresas Active Record
 * @author  <your-name-here>
 */
class Empresa extends TRecord
{
    const TABLENAME = 'public.empresas';
    const PRIMARYKEY= 'emp_id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $bairro;
    private $estado_federativo;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('emp_nome_fantasia');
        parent::addAttribute('emp_razao_social');
        parent::addAttribute('emp_cnpj');
        parent::addAttribute('emp_inscricao_estadual');
        parent::addAttribute('emp_inscricao_municipal');
        parent::addAttribute('emp_cep');
        parent::addAttribute('emp_logradouro');
        parent::addAttribute('emp_numero');
        parent::addAttribute('emp_uf');
        parent::addAttribute('emp_cidade');
        parent::addAttribute('emp_bairro');
        //    parent::addAttribute('emp_efs_id');
    //    parent::addAttribute('emp_brr_id');
    }

    
    /**
     * Method set_bairro
     * Sample of usage: $empresas->bairro = $object;
     * @param $object Instance of Bairro
     */
    /*
    public function set_bairro(Bairro $object)
    {
        $this->bairro = $object;
        $this->bairro_brr_id = $object->brr_id;
    }
    */
    /**
     * Method get_bairro
     * Sample of usage: $empresas->bairro->attribute;
     * @returns Bairro instance
     */
    /*
    public function get_bairro()
    {
        // loads the associated object
        if (empty($this->bairro))
            $this->bairro = new Bairro($this->bairro_brr_id);
    
        // returns the associated object
        return $this->bairro;
    }
    */
    
    
    /**
     * Method set_estado_federativo
     * Sample of usage: $empresas->estado_federativo = $object;
     * @param $object Instance of EstadoFederativo
     */
        /*
    public function set_estado_federativo(EstadoFederativo $object)
    {
        $this->estado_federativo = $object;
        $this->estado_federativo_efs_id = estado_federativo_efs_id;
    }
    */
    
    /**
     * Method get_estado_federativo
     * Sample of usage: $empresas->estado_federativo->attribute;
     * @returns EstadoFederativo instance
     */
        
        /*
    public function get_estado_federativo()
    {
        // loads the associated object
        if (empty($this->estado_federativo))
            $this->estado_federativo = new EstadoFederativo($this->estado_federativo_id);
    
        // returns the associated object
        return $this->estado_federativo;
    }
    
*/

}
