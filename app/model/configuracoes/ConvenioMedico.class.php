<?php

//namespace model\medico;

use Adianti\Database\TRecord;
class ConvenioMedico extends TRecord {
	
	const TABLENAME = 'convenios_medicos';
	const PRIMARYKEY= 'cmo_id';
	const IDPOLICY =  'serial'; // {max, serial}
	
}

?>