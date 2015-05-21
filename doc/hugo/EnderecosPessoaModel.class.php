<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EnderecosPessoa
 *
 * @author hugo
 */
class EnderecosPessoaModel extends TRecord
{
    const TABLENAME = 'sch_api_sgs_data.enderecos_pessoa';
    const PRIMARYKEY= 'id';
    //const IDPOLICY =  'max'; // {max, serial}
    const IDPOLICY =  'serial'; // {max, serial}
    
    public function get_descricao()
    {
        return $this->epe_tipo_endereco == '1' ? 'Residencial' : 'Comercial';
    }
    
}
