<?php
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Container\TTable;
use Adianti\Widget\Form\TLabel;
//namespace control\consultas;

class AnamneseForm extends TPage
{

    protected $form;
    
    function __construct($id = NULL) {
        
        parent::__construct($id = NULL);
        
        $this->form = new TForm('form_anamnese');
        $this->form->class = 'tform';
        $this->form->style = 'width: 100%';
        
        $tblForm = new TTable;
        $tblForm->width = '100%;';
       
        $row = $tblForm->addRow();
        $row->class = 'tformtitle';
        $row->addCell(new TLabel('Anamnese'))->colspan = 4;
        
        
        
        
        $this->form->add($tblForm);

    }
    
    public function onEdit(){}
}

?>