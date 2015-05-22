<?php
class BoletoForm extends TPage {
	private $form;

	function __construct() {
		parent::__construct();

		$this->form = new TQuickForm;
		$this->form->class = 'tform';
		$this->form->style = 'width:640px';
		$this->form->setFormTitle('Gerar Boleto');

		$numero = new TEntry('nosso_numero');
		$vencimento = new TDate('data_vencimento');
		$valor = new TEntry('valor_boleto'); // Com vírgula e sempre com duas casas depois da virgula
		$sacado = new TEntry('sacado');
		$endereco1 = new TEntry('endereco1');
		$endereco2 = new TEntry('endereco2');
		$demonstrativo = new TText('demonstrativo');
		$instrucoes = new TText('instrucoes');
		$numero->setValue('1234');
		$valor->setNumericMask(2, ',', '.');
		$valor->setValue('100,00');
		$vencimento->setMask('dd/mm/yyyy');
		$vencimento->setValue(date('d/m/Y', mktime(0, 0, 0, date("m"), date("d") + 30, date("Y"))));
		$sacado->setValue('Pedro');
		$endereco1->setValue('Rua Júlio de Castilhos');
		$endereco2->setValue('Porto Alegre, CEP: 88.888-888');
		$demonstrativo->setValue("Pagamento inscrição ...\nMensalidade referente a .. \nBoleto Automático");
		$instrucoes->setValue("- Sr. Caixa, cobrar multa de 2% após o vencimento\n- Receber até 10 dias após o vencimento\n- Em caso de dúvidas entre em contato conosco: email@email.com");

		$this->form->addQuickField('Número', $numero, 40);
		$this->form->addQuickField('Vencimento', $vencimento, 100);
		$this->form->addQuickField('Valor', $valor, 100);
		$this->form->addQuickField('Sacado', $sacado, 200);
		$this->form->addQuickField('Endereço 1', $endereco1, 400);
		$this->form->addQuickField('Endereço 2', $endereco2, 400);
		$this->form->addQuickField('Demonstrativo', $demonstrativo, 400);
		$this->form->addQuickField('Instruções', $instrucoes, 400);
		$demonstrativo->setSize(400, 100);
		$instrucoes->setSize(400, 100);

		$this->form->addQuickAction('Gerar', new TAction(array($this, 'onGenerate')), 'ico_apply.png');

		parent::add($this->form);
	}

	public function onGenerate($param) {
		$data = $this->form->getData();

		TApplication::loadPage('BoletoView', 'onGenerate', (array) $data);
	}
}
?>