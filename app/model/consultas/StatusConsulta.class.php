<?php
/**
 * StatusConsultas Active Record
 * @author  <your-name-here>
 */
class StatusConsulta extends TRecord
{
    const TABLENAME = 'public.status_consultas';
    const PRIMARYKEY= 'scs_id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('scs_descricao');
        parent::addAttribute('scs_cor');
    }


}
