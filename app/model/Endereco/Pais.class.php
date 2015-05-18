<?php
//namespace Endereco;

use Adianti\Database\TRecord;

class Pais extends TRecord
{
    const TABLENAME  = 'pais';
    const PRIMARYKEY = 'pai_id';
    const IDPOLICY   = 'serial'; 
}

?>