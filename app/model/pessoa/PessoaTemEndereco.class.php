<?php
use Adianti\Database\TRecord;
//namespace Pessoa;

class PessoaTemEndereco extends TRecord
{
    const TABLENAME = 'pessoas_tem_enderecos';
    const PRIMARYKEY = 'pte_id';
    const IDPOLICY = 'serial';
}

?>