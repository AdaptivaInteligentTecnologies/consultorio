<?php
/**
 * Cid10 Active Record
 * @author  <your-name-here>
 */
class Cid10 extends TRecord
{
    const TABLENAME = 'public.cid10';
    const PRIMARYKEY= 'cid_id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cid_descricao');
    }


}
