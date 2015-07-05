<?php
/**
 * ProcedimentosMedicos Active Record
 * @author  <your-name-here>
 */
class ProcedimentoProfissional extends TRecord
{
    const TABLENAME = 'public.procedimentos_profissionais';
    const PRIMARYKEY= 'pms_id';
    const IDPOLICY =  'max'; // {max, serial}

    protected $simboloMoeda = 'R$';
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pms_descricao');
        parent::addAttribute('pms_valor');
        parent::addAttribute('pms_cor');
    }
    
    
    
    public function get_valor()
    {
        return number_format($this->pms_valor, 2, ',', '.');
    }


}
