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
class EvolucaoForm extends TPage
{

    protected $form;

    function __construct($id = NULL)
    {
        parent::__construct($id = NULL);
        
        $this->form = new TForm('form_evolucao');
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
        $cns_evolucao = new THtmlEditor('cns_evolucao');
        $cns_evolucao->setSize(500);
        
        // HD - Hipótese diagnóstica
        $tblEvolucao = new TTable('tblEvolucao');
        
        $tblEvolucao->width = '270px;';
        
        $row = $tblEvolucao->addRow();
        $lbl = new TElement('h2');
        $lbl->add('Evolução');
        $row->addCell($lbl);
        $row->addCell($btnSalvar)->align = "right";
        $row = $tblEvolucao->addRow();
        $row->addCell($cns_evolucao)->colspan = 2;
        // $row->addCell($cns_pa_sistolica )->colspan = '2';
        
        $this->form->setFields(array(
            $btnSalvar
        ));
        
        $divEvolucao = new TVBox('tvboxConduta');
        $divEvolucao->add($tblEvolucao);
        $this->form->add($divEvolucao);
        
        parent::add($this->form);
    }

    public static function onSave($param)
    {
        $consulta = new Consulta(TSession::getValue('session_atendimento_consulta_id'));
        try {
            TTransaction::open('consultorio');
            $consulta->cns_evolucao = $param['cns_evolucao'];
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