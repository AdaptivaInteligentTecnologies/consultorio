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
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pms_descricao');
        parent::addAttribute('pms_cor');
    }


}