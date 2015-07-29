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
use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use adianti\widget\dialog\TToast;

// namespace control\consultas;
class AnamneseForm extends TPage
{

    protected $form;

    function __construct($id = NULL)
    {
        parent::__construct($id = NULL);
        
        $this->form = new TForm('form_anamnese');
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
        
        // fields Anamnese
        $cns_historia_medica_patologica_pregressa = new THtmlEditor('cns_historia_medica_patologica_pregressa');
        $cns_historia_medica_patologica_pregressa->setSize(500);
        
        $cns_hf = new THtmlEditor('cns_hf');
        $cns_hf->setSize(500);
        
        $cns_queixa_principal = new THtmlEditor('cns_queixa_principal');
        $cns_queixa_principal->setSize(500);
        
        $cns_historico_doenca_atual = new THtmlEditor('cns_historico_doenca_atual');
        $cns_historico_doenca_atual->setSize(500);
        
        $cns_perfil = new THtmlEditor('cns_perfil');
        $cns_perfil->setSize(500);
        
        $cns_hipotese_diagnostica = new THtmlEditor('cns_hipotese_diagnostica');
        $cns_hipotese_diagnostica->setSize(500);
        
        $cns_hpfs = new THtmlEditor('cns_hpfs');
        $cns_hpfs->setSize(500);
        
        $cns_medimento_em_uso = new THtmlEditor('cns_medimento_em_uso');
        $cns_medimento_em_uso->setSize(500);
        
        $cns_observacoes = new THtmlEditor('cns_observacoes');
        $cns_observacoes->setSize(500);
        
        // HPP, HPM - História médica pregressa / História patológica pregressa
        $tblHPP_HPM = new TTable();
        $tblHPP_HPM->width = '500px;';
        $row = $tblHPP_HPM->addRow();
        $lbl = new TElement('h2');
        $lbl->add('HPP, HPM - História médica pregressa / História patológica pregressa');
        $row->addCell($lbl);
        $row->addCell($btnSalvar)->align = "right";
        $row = $tblHPP_HPM->addRow();
        $row->addCell($cns_historia_medica_patologica_pregressa)->colspan = '2';
        
        // HF - Histórico familiar
        $tblHF = new TTable();
        $tblHF->width = '500px;';
        $row = $tblHF->addRow();
        $lbl = new TElement('h2');
        $lbl->add('HF - Histórico Familiar');
        $row->addCell($lbl);
        $row->addCell($btnSalvar)->align = "right";
        $row = $tblHF->addRow();
        $row->addCell($cns_hf)->colspan = '2';
        
        // QP Quixa Principal
        $tblQP = new TTable();
        $tblQP->width = '500px;';
        $row = $tblQP->addRow();
        $lbl = new TElement('h2');
        $lbl->add('QP - Queixa Principal');
        $row->addCell($lbl);
        $row->addCell($btnSalvar)->align = "right";
        $row = $tblQP->addRow();
        $row->addCell($cns_queixa_principal)->colspan = '2';
        
        // HDA - Histórico da doença atual
        $tblHDA = new TTable();
        $tblHDA->width = '500px;';
        $row = $tblHDA->addRow();
        $lbl = new TElement('h2');
        $lbl->add('HDA - Histórico da doença atual');
        $row->addCell($lbl);
        $row->addCell($btnSalvar)->align = "right";
        $row = $tblHDA->addRow();
        $row->addCell($cns_historico_doenca_atual)->colspan = '2';
        
        // Perfil - Perfil do paciente
        $tblPERFIL = new TTable();
        $tblPERFIL->width = '500px;';
        $row = $tblPERFIL->addRow();
        $lbl = new TElement('h2');
        $lbl->add('PP - Perfil do paciente');
        $row->addCell($lbl);
        $row->addCell($btnSalvar)->align = "right";
        $row = $tblPERFIL->addRow();
        $row->addCell($cns_perfil)->colspan = '2';
        
        // HD - Hipótese diagnóstica
        $tblHipoteseDiagnostica = new TTable();
        $tblHipoteseDiagnostica->width = '500px;';
        $row = $tblHipoteseDiagnostica->addRow();
        $lbl = new TElement('h2');
        $lbl->add('HD - Hipótese diagnóstica');
        $row->addCell($lbl);
        $row->addCell($btnSalvar)->align = "right";
        $row = $tblHipoteseDiagnostica->addRow();
        $row->addCell($cns_hipotese_diagnostica)->colspan = '2';
        
        // HPFS - Hipótese diagnóstica
        $tblHPFS = new TTable();
        $tblHPFS->width = '500px;';
        $row = $tblHPFS->addRow();
        $lbl = new TElement('h2');
        $lbl->add('HPFS - História pessoal (fisiológica) e história social');
        $row->addCell($lbl);
        $row->addCell($btnSalvar)->align = "right";
        $row = $tblHPFS->addRow();
        $row->addCell($cns_hpfs)->colspan = '2';
        
        // Medicamentos
        
        $tblMedicamentosEmUso = new TTable();
        $tblMedicamentosEmUso->width = '500px;';
        $row = $tblMedicamentosEmUso->addRow();
        $lbl = new TElement('h2');
        $lbl->add('Medicamentos em uso');
        $row->addCell($lbl);
        $row->addCell($btnSalvar)->align = "right";
        $row = $tblMedicamentosEmUso->addRow();
        $row->addCell($cns_medimento_em_uso)->colspan = '2';
        
        $this->form->setFields(array(
            $btnSalvar
        ));
        
        $divAnamnese = new TVBox('tvboxAnamnese');
        $divAnamnese->add($tblHPP_HPM);
        $divAnamnese->add($tblHF);
        $divAnamnese->add($tblQP);
        $divAnamnese->add($tblHDA);
        $divAnamnese->add($tblPERFIL);
        $divAnamnese->add($tblHipoteseDiagnostica);
        $divAnamnese->add($tblHPFS);
        $divAnamnese->add($tblMedicamentosEmUso);
        
        $this->form->add($divAnamnese);
        
        parent::add($this->form);
    }

    public static function onSave($param)
    {
        $consulta = new Consulta(TSession::getValue('session_atendimento_consulta_id'));
        try {
            TTransaction::open('consultorio');
            $consulta->cns_hpp_hmp = $param['cns_historia_medica_patologica_pregressa'];
            $consulta->cns_hf = $param['cns_hf'];
            $consulta->cns_hpfs = $param['cns_hpfs'];
            $consulta->cns_queixa_principal = $param['cns_queixa_principal'];
            $consulta->cns_historico_doenca_atual = $param['cns_historico_doenca_atual'];
            $consulta->cns_perfil = $param['cns_perfil'];
            $consulta->cns_hipotese_diagnostica = $param['cns_hipotese_diagnostica'];
            $consulta->cns_medimento_em_uso = $param['cns_medimento_em_uso'];
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