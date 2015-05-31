<?php

//namespace model\medico;

use Adianti\Database\TRecord;
class TipoContato extends TRecord {
	
	const TABLENAME = 'tipos_contatos';
	const PRIMARYKEY= 'tco_id';
	const IDPOLICY =  'max'; // {max, serial}
	
}

?>