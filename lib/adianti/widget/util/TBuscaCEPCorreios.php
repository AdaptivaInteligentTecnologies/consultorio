<?php
namespace adianti\widget\util;

class TBuscaCEPCorreios
{
    private $campoTabela;

    public function __construct($cep = NULL)
    {

        if (empty($cep))
        {
            return;
        }
        
        
        $postCorreios = "relaxation=".$cep."&TipoCep=ALL&semelhante=N&cfm=1&Metodo=listaLogradouro&TipoConsulta=relaxation&StartRow=1&EndRow=10";
        $cURL = curl_init("http://www.buscacep.correios.com.br/servicos/dnec/consultaLogradouroAction.do");
        // seta opcoes para fazer a requisicao
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HEADER, false);
        curl_setopt($cURL, CURLOPT_POST, true);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $postCorreios);

        // faz a requisicao e retorna o conteudo do endereco
        $saida = curl_exec($cURL);
        
        
        curl_close($cURL);// encerra e retorna os dados
        
        $saida = utf8_encode($saida); // codifica conteudo para utf-8
       // print_r($saida);
        
        $this->campoTabela = array();
        
        // pega apenas o conteudo das tds e transforma em um array
        preg_match_all('@<td (.*?)<\/td>@i', $saida, $this->campoTabela);
        
    }
    
    public function getLogradouro()
    {
        return (count($this->campoTabela[0]) == 5 ) ? strip_tags($this->campoTabela[0][0]) : '';
    }

    public function getBairro()
    {
        return (count($this->campoTabela[0]) == 5 ) ? strip_tags($this->campoTabela[0][1]) : '';
    }
    
    public function getCidade()
    {
        return (count($this->campoTabela[0]) == 5 ) ? strip_tags($this->campoTabela[0][2]) : strip_tags($this->campoTabela[0][0]) ;
    }
    
    public function getUf()
    {
        return (count($this->campoTabela[0]) == 5 ) ? strip_tags($this->campoTabela[0][3]) : strip_tags($this->campoTabela[0][1]) ;
        
    }
    
    
}

?>