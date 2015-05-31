<?php

use Adianti\Database\TRecord;
//namespace model\configuracoes;

class Contato extends TRecord{
	const TABLENAME = 'contatos';
	const PRIMARYKEY= 'cto_id';
	const IDPOLICY =  'max'; // {max, serial}
}

?>