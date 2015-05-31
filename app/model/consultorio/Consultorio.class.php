<?php
use Adianti\Database\TRecord;
//namespace model\consultorio;

class Consultorio extends TRecord
{
    const  TABLENAME = 'consultorios';
    const  PRIMARYKEY = 'con_id';
    const IDPOLICY = 'max';
}

?>