<?php
/**
 * Consultorios Active Record
 * @author  <your-name-here>
 */
class Consultorio extends TRecord
{
    const TABLENAME = 'public.consultorios';
    const PRIMARYKEY= 'con_id';
    const IDPOLICY =  'max'; // {max, serial}
    const CACHECONTROL = 'TAPCache';
    
    
    private $empresa;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('con_nome');
        parent::addAttribute('con_emp_id');
        parent::addAttribute('con_ini_expediente');
        parent::addAttribute('con_fim_expediente');
        parent::addAttribute('con_fecha_para_almoco');
        parent::addAttribute('con_ini_almoco');
        parent::addAttribute('con_fim_almoco');
        parent::addAttribute('con_fun_segunda');
        parent::addAttribute('con_fun_terca');
        parent::addAttribute('con_fun_quarta');
        parent::addAttribute('con_fun_quinta');
        parent::addAttribute('con_fun_sexta');
        parent::addAttribute('con_fun_sabado');
        parent::addAttribute('con_fun_domingo');
    }

    
    /**
     * Method set_empresa
     * Sample of usage: $consultorios->empresa = $object;
     * @param $object Instance of Empresa
     */
    public function set_empresa(Empresa $object)
    {
        $this->empresa = $object;
        $this->empresa_id = $object->id;
    }
    
    /**
     * Method get_empresa
     * Sample of usage: $consultorios->empresa->attribute;
     * @returns Empresa instance
     */
    public function get_empresa()
    {
        // loads the associated object
        if (empty($this->empresa))
            $this->empresa = new Empresa($this->empresa_id);
    
        // returns the associated object
        return $this->empresa;
        
        
    }
    


}
