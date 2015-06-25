<?php
//namespace model\Profissional;

use Adianti\Database\TRecord;
class ProfissionalTemEspecialidade extends TRecord
{
    const TABLENAME     = 'profissionais_tem_especialidades';
    const PRIMARYKEY    = 'mte_id';
    const IDPOLICY      = 'max';

    public function __construct($id = NULL)
    {
        parent::__construct($id);        
        parent::addAttribute('mte_pfs_id');
        parent::addAttribute('mte_ems_id');
    }
    
    
}

?>