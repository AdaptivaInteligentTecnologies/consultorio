<?php
/**
 * StatusConsultas Active Record
 * @author  <your-name-here>
 */
class StatusAgendamento extends TRecord
{
    const TABLENAME = 'public.status_agendamento';
    const PRIMARYKEY= 'sas_id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('sas_descricao');
        parent::addAttribute('sas_cor');
    }


}
