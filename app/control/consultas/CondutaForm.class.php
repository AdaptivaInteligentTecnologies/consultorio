<?php
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Container\TTable;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\THtmlEditor;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Form\TButton;
use Adianti\Control\TPage;
use Adianti\Widget\Container\TVBox;
use Adianti\Registry\TSession;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Base\TStyle;
use adianti\widget\dialog\TToast;

// namespace control\consultas;
class CondutaForm extends TPage
{

    protected $form;

    function __construct($id = NULL)
    {
        parent::__construct($id = NULL);
        
        $this->form = new TForm('form_conduta');
        $this->form->style = 'width: 100%';
        
        $btnSalvar = new TButton('save');
        $btnSalvar->setImage('bs:saved white');
        $btnSalvar->class = 'btn btn-xs btn-success';
        $btnSalvar->popover = 'true';
        $btnSalvar->poptitle = 'Informação';
        $btnSalvar->popcontent = 'Salva o texto atual';
        $actBtnSalvar = new TAction(array(
            $this,
            'onSave'
        ));
        
        $btnSalvar->setAction($actBtnSalvar, 'Salvar');
        
        // fields ExameFisico
        
        // fields Anamnese
        $cns_conduta = new THtmlEditor('cns_conduta');
        $cns_conduta->setSize(500);
        
        // HD - Hipótese diagnóstica
        $tblConduta = new TTable('tblConduta');
        
        $tblConduta->width = '270px;';
        
        $row = $tblConduta->addRow();
        $lbl = new TElement('h2');
        $lbl->add('Conduta');
        $row->addCell($lbl);
        $row->addCell($btnSalvar)->align = "right";
        $row = $tblConduta->addRow();
        $row->addCell($cns_conduta)->colspan = 2;
        // $row->addCell($cns_pa_sistolica )->colspan = '2';
        
        $this->form->setFields(array(
            $btnSalvar
        ));
        
        $divConduta = new TVBox('tvboxConduta');
        $divConduta->add($tblConduta);
        $this->form->add($divConduta);
        
        parent::add($this->form);
    }

    public static function onSave($param)
    {
        $consulta = new Consulta(TSession::getValue('session_atendimento_consulta_id'));
        try {
            TTransaction::open('consultorio');
            $consulta->cns_conduta = $param['cns_conduta'];
            $consulta->store();
            TTransaction::close();
            new TToast('Texto salvo');
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('info', 'Erro inesperado ao salvar dados. ERRO: ' . $e->getMessage());
        }
    }

    public function onEdit()
    {
        //
    }
}

?>