<?php
class Prontuario extends TPage
{
    public function __construct()
    {
        parent::__construct();
        parent::add(new TLabel('Prontuário médico'));
    }
}
?>