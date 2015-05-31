<?php

use Adianti\Widget\Dialog\TMessage;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;


//namespace model\medico;

class MedicoView extends TRecord{

    const TABLENAME  = 'medicos_list_view';
    const PRIMARYKEY = 'med_id';
    const IDPOLICY   = 'max';
    
}

?>