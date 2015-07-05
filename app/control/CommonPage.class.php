<?php
class CommonPage extends TPage
{
    public function __construct()
    {
        parent::__construct();
        parent::add(new TLabel('Página inicial não setada'));
    }
}
?>