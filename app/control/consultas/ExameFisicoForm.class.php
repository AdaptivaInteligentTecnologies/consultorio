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
class ExameFisicoForm extends TPage
{

    protected $form;

    function __construct($id = NULL)
    {
        parent::__construct($id = NULL);
        
        $this->form = new TForm('form_exame_fisico');
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
        
        $cns_pa_sistolica = new TEntry('cns_pa_sistolica');
        $cns_pa_sistolica->setSize('50');
        
        $cns_pa_diastolica = new TEntry('cns_pa_diastolica');
        $cns_pa_diastolica->setSize('50');
        
        $cns_frequencia_cardiaca = new TEntry('cns_fc');
        $cns_frequencia_cardiaca->setSize('50');
        
        $cns_altura = new TEntry('cns_altura');
        $cns_altura->setSize('50');
        
        $cns_peso = new TEntry('cns_peso');
        $cns_peso->setSize('50');
        
        $cns_imc = new TEntry('cns_imc');
        
        // HD - Hipótese diagnóstica
        $tblExameFisico = new TTable('tblExameFisico');
        
        $tblExameFisico->width = '270px;';
        $tblExameFisico->style = 'boder: 1px solid #ccc;';
        
        $row = $tblExameFisico->addRow();
        $lbl = new TElement('h2');
        $lbl->add('Avaliação física');
        $row->addCell($lbl);
        $row->addCell($btnSalvar)->align = "right";
        $row = $tblExameFisico->addRow();
        $row->addCell('PA - Sistólica (mmHg): ')->align = 'right';
        $row->addCell($cns_pa_sistolica)->colspan = 2;
        $row = $tblExameFisico->addRow();
        $row->addCell('PA - Diastólica (mmHg): ')->align = 'right';
        $row->addCell($cns_pa_diastolica)->colspan = 2;
        
        $row = $tblExameFisico->addRow();
        $row->addCell('Frequência cardíaca (bpm): ')->align = 'right';
        $row->addCell($cns_frequencia_cardiaca)->colspan = 2;
        $row = $tblExameFisico->addRow();
        $row->addCell('Altura (m): ')->align = 'right';
        $row->addCell($cns_altura)->colspan = 2;
        $row = $tblExameFisico->addRow();
        $row->addCell('Peso (kg):')->align = 'right';
        $row->addCell($cns_peso)->colspan = 2;
        // $row->addCell($cns_pa_sistolica )->colspan = '2';
        
        $this->form->setFields(array(
            $btnSalvar
        ));
        
        $divExameFisico = new TVBox('tvboxExameFisico');
        $divExameFisico->add($tblExameFisico);
        $this->form->add($divExameFisico);
        
        parent::add($this->form);
    }

    public static function onSave($param)
    {
        $consulta = new Consulta(TSession::getValue('session_atendimento_consulta_id'));
        try {
            TTransaction::open('consultorio');
            
            $consulta->cns_pa_sistolica = $param['cns_pa_sistolica'];
            $consulta->cns_pa_diastolica = $param['cns_pa_diastolica'];
            $consulta->cns_fc = $param['cns_fc'];
            $consulta->cns_altura = $param['cns_altura'];
            $consulta->cns_peso = $param['cns_peso'];
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