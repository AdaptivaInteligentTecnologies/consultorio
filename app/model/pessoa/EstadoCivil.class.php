<?php
use Adianti\Database\TRecord;
//namespace model\pessoa;

class EstadoCivil extends TRecord
{
    const TABLENAME     = 'estados_civis';
    const PRIMARYKEY    = 'ecs_id';
    const IDPOLICY      = 'max';
    
    
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('ecs_descricao');
    }
}

?>