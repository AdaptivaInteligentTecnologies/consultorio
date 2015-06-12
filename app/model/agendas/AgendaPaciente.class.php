<?php
use Adianti\Database\TRecord;
//namespace model;

class AgendaPaciente extends TRecord
{
    const TABLENAME     = 'agenda_pacientes';
    const PRIMARYKEY    = 'aps_id';
    const IDPOLICY      = 'max';
    
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('aps_pts_id');
        parent::addAttribute('aps_med_id');
        parent::addAttribute('aps_tas_id');
        parent::addAttribute('aps_con_id');
        parent::addAttribute('aps_cms_id');
        parent::addAttribute('aps_nome_paciente');
        parent::addAttribute('aps_data_cadastro');
        parent::addAttribute('aps_data_agendada');
        parent::addAttribute('aps_hora_agendada');
    }
}

?>