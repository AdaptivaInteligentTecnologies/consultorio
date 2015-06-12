<?php
//namespace model\coreseracas;

use Adianti\Database\TRecord;

class CorRaca extends TRecord
{
        
    const TABLENAME     = 'cores_racas';
    const PRIMARYKEY    = 'crs_id';
    const IDPOLICY      = 'max';

    function __construct($id = NULL)
    {
        parent::__construct($id);
        
        
    }
}

?>