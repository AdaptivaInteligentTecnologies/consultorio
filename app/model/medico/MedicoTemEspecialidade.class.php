<?php
//namespace model\medico;

use Adianti\Database\TRecord;
class MedicoTemEspecialidade extends TRecord
{
    const TABLENAME     = 'medicos_tem_especialidades';
    const PRIMARYKEY    = 'mte_id';
    const IDPOLICY      = 'max';

    public function __construct($id = NULL)
    {
        parent::__construct($id);        
        parent::addAttribute('mte_med_id');
        parent::addAttribute('mte_ems_id');
    }
    
    
}

?>