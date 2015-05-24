<?php

//namespace model\medico;

use Adianti\Database\TRecord;
class ConvenioMedico extends TRecord {
	
	const TABLENAME = 'convenios';
	const PRIMARYKEY= 'cns_id';
	const IDPOLICY =  'max'; // {max, serial}
	
}

?>