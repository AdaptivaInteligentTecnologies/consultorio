<?php
namespace control\consultas;

use Adianti\Widget\Base\TElement;

class BlocoTimeLine extends TElement
{

    protected $campo;

    protected $descricao;

    private $elementoH2;

    private $elementoP;

    private $elementoDiv;

    protected $size;

    function __construct($campoRetorno = NULL, $descricaoDoCampo = NULL, $size = '400px')
    {
        parent::__construct();
        
        $this->elementoH2 = new TElement('h2');
        $this->elementoP = new TElement('p');
        $this->elementoDiv = new TElement('div');
        $this->elementoDiv->style = " word-wrap:break-word; display:block;";

        $this->campo = $campoRetorno;
        $this->descricao = $descricaoDoCampo;
        $this->size = $size;
    }

    public function setCampo($campo)
    {
        $this->campo = $campo;
        return $this;
    }

    public function setDescricao($desc)
    {
        $this->descricao = $desc;
        return $this;
    }
    
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    public function getCampo()
    {
        return $this->campo;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function getBloco()
    {
        if (! empty($this->getCampo())) {
            $this->elementoH2->add($this->getDescricao());
            $this->elementoP->add($this->getCampo());
            $this->elementoP->add('<br />');
            $this->elementoDiv->add($this->elementoH2);
            $this->elementoDiv->add($this->elementoP);
            if ($this->size) {
                $this->elementoDiv->width = $this->size;
                //$this->elementoDiv->height = '300px';
            }
            
            return $this->elementoDiv;
        }
    }

    public function show()
    {
        parent::show();
    }
}

?>