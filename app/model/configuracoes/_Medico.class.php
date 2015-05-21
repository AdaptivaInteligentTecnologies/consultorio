<?php

//namespace model\medico;

use Adianti\Database\TRecord;
class Medico extends TRecord{
    const TABLENAME  = 'medicos';
    const PRIMARYKEY = 'med_id';
    const IDPOLICY   = 'serial';

}

?>