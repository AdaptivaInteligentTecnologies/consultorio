<?php
use Adianti\Database\TRecord;
//namespace model;

class CadastroMedico extends TRecord
{
    const TABLENAME  = 'medicos';
    const PRIMARYKEY = 'med_id';
    const IDPOLICY   = 'serial';

}

?>