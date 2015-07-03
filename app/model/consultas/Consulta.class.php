<?php
/**
 * Consultas Active Record
 * @author  <your-name-here>
 */
class Consulta extends TRecord
{
    const TABLENAME = 'public.consultas';
    const PRIMARYKEY= 'acs_id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $cid10;
    private $profissional;
    private $paciente;
    private $procedimento;
    private $status_consulta;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
/*
 *      parent::addAttribute('acs_cid10_id');
        parent::addAttribute('acs_pfs_id');
        parent::addAttribute('acs_pts_id');
        parent::addAttribute('acs_data_hora_ini_consulta');
        parent::addAttribute('acs_data_hora_fim_consulta');
        parent::addAttribute('acs_pressao_arterial_sistolica');
        parent::addAttribute('acs_pressao_arterial_diastolica');
        parent::addAttribute('acs_peso');
        parent::addAttribute('acs_altura');
        parent::addAttribute('acs_fc');
        parent::addAttribute('acs_queixa_principal');
        parent::addAttribute('acs_hda');
        parent::addAttribute('acs_hmp');
        parent::addAttribute('acs_observacao');
        parent::addAttribute('acs_status');
 */
    }

    
    public function set_idade(Paciente $object)
    {
        $this->paciente = $object;
        $this->paciente_id = $object->id;
    }
    
    public function get_idade()
    {
        /*
        $tz  = new DateTimeZone('Europe/Brussels');
        $age = DateTime::createFromFormat('d/m/Y', '12/02/1973', $tz)
        ->diff(new DateTime('now', $tz))
        ->y;
        */
        $date = new DateTime('1976-08-03');
        $now = new DateTime();
        $interval = $now->diff($date);
        return $interval->y;
    }
    
    
    
    /**
     * Method set_cid10
     * Sample of usage: $consultas->cid10 = $object;
     * @param $object Instance of Cid10
     */
    public function set_cid10(Cid10 $object)
    {
        $this->cid10 = $object;
        $this->cid10_id = $object->id;
    }
    
    /**
     * Method get_cid10
     * Sample of usage: $consultas->cid10->attribute;
     * @returns Cid10 instance
     */
    public function get_cid10()
    {
        // loads the associated object
        if (empty($this->cid10))
            $this->cid10 = new Cid10($this->cid10_id);
    
        // returns the associated object
        return $this->cid10;
    }
    
    public function set_procedimento(ProcedimentoProfissional  $object)
    {
        $this->procedimento = $object;
        $this->procedimento_id = $object->id;
    }
    
    public function get_procedimento()
    {
        // loads the associated object
        if (empty($this->procedimento))
            $this->procedimento = new ProcedimentoProfissional($this->procedimento_id);
    
        // returns the associated object
        return $this->procedimento;
    }
    
    /**
     * Method set_profissional
     * Sample of usage: $consultas->profissional = $object;
     * @param $object Instance of profissional
     */
    public function set_profissional(Profissional $object)
    {
        $this->profissional = $object;
        $this->profissional_id = $object->id;
    }
    
    
    /**
     * Method get_profissional
     * Sample of usage: $consultas->profissional->attribute;
     * @returns profissional instance
     */
    public function get_profissional()
    {
        // loads the associated object
        if (empty($this->profissional))
            $this->profissional = new Profissional($this->profissional_id);
    
        // returns the associated object
        return $this->profissional;
    }
    
    
    /**
     * Method set_paciente
     * Sample of usage: $consultas->paciente = $object;
     * @param $object Instance of Paciente
     */
    public function set_paciente(Paciente $object)
    {
        $this->paciente = $object;
        $this->paciente_id = $object->id;
    }
    
    /**
     * Method get_paciente
     * Sample of usage: $consultas->paciente->attribute;
     * @returns Paciente instance
     */
    public function get_paciente()
    {
        // loads the associated object
        if (empty($this->paciente))
            $this->paciente = new Paciente($this->paciente_id);
    
        // returns the associated object
        return $this->paciente;
    }
    
    
    /**
     * Method set_status_consulta
     * Sample of usage: $consultas->status_consulta = $object;
     * @param $object Instance of StatusConsulta
     */
    public function set_status_consulta(StatusConsulta $object)
    {
        $this->status_consulta = $object;
        $this->status_consulta_id = $object->id;
    }
    
    /**
     * Method get_status_consulta
     * Sample of usage: $consultas->status_consulta->attribute;
     * @returns StatusConsulta instance
     */
    public function get_status_consulta()
    {
        // loads the associated object
        if (empty($this->status_consulta))
            $this->status_consulta = new StatusConsulta($this->status_consulta_id);
    
        // returns the associated object
        return $this->status_consulta;
    }
    


}
