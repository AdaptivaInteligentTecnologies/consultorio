<?php
use Adianti\Database\TRecord;
//namespace Pessoa;

class TipoContato extends TRecord
{
    const TABLENAME = 'tipos_contatos';
    const PRIMARYKEY = 'tco_id';
    const IDPOLICY = 'serial';

}

?>