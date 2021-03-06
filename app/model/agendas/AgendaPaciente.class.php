<?php
use Adianti\Database\TRecord;
use Adianti\Database\TCriteria;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Database\TTransaction;
use Adianti\Widget\Container\TTableRow;
//namespace model;

class AgendaPaciente extends TRecord
{
    const TABLENAME     = 'agenda_pacientes';
    const PRIMARYKEY    = 'aps_id';
    const IDPOLICY      = 'max';
    const CACHECONTROL  = 'TAPCache';
    
    
    protected $nomeProfissional;
    
    public function __construct($id = NULL)
    {
        
        parent::__construct($id);
        /*
        parent::addAttribute('aps_pts_id');
        parent::addAttribute('aps_pfs_id');
        parent::addAttribute('aps_pms_id');
        parent::addAttribute('aps_cps_id');
        parent::addAttribute('aps_nome_paciente');
        parent::addAttribute('aps_data_nascimento');
        parent::addAttribute('aps_data_cadastro');
        parent::addAttribute('aps_data_agendada');
        parent::addAttribute('aps_hora_agendada');
        parent::addAttribute('aps_status');
        parent::addAttribute('aps_confirmado');
        parent::addAttribute('aps_telefone_contato1');
        parent::addAttribute('aps_telefone_contato2');
        */
    }
    
    
    public function get_nomeProfissional()
    {
        if (empty($this->nomeProfissional))
        {
            $this->nomeProfissional = new Profissional($this->aps_pfs_id);
        }
        
        return $this->nomeProfissional->pfs_nome;
    }
}

?>