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
    
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('aps_pts_id');
        parent::addAttribute('aps_pfs_id');
        parent::addAttribute('aps_pms_id');
        parent::addAttribute('aps_cps_id');
        parent::addAttribute('aps_nome_paciente');
        parent::addAttribute('aps_data_cadastro');
        parent::addAttribute('aps_data_agendada');
        parent::addAttribute('aps_hora_agendada');
    }
    
    /*
    public function getEventos(Array $periodo)
    {
        try {
            
            
            $data_ini = strtotime($periodo[0]);
            $data_fim = strtotime($periodo[1]);
            if ($data_ini<=$data_fim){
                
                TTransaction::open('consultorio');
                $criteria = new TCriteria();
                $criteria->add(new TFilter('data_hora_agendamento', 'between', $data_ini,$data_fim));
                $repository = new TRepository('AgendaPaciente');
                $eventos = $repository->load($criteria);
                echo json_encode($eventos);
                TTransaction::close();            
            }
                
        }catch (Exception $e){
            new TMessage('error', 'Erro: '.$e->getMessage());
            TTransaction::close();
        }
    }
    */
}

?>