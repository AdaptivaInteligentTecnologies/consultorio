class BoletoView extends TPage
{
    public function onGenerate($param)
    {
        $dadosboleto = $param;
        $dadosboleto["numero_documento"] = $dadosboleto["nosso_numero"];
        $dadosboleto["valor_boleto"] = str_replace(".", "",$dadosboleto["valor_boleto"]);
        $dadosboleto["valor_boleto"] = str_replace(",", ".",$dadosboleto["valor_boleto"]);
        $dadosboleto["valor_boleto"] = number_format($dadosboleto["valor_boleto"], 2, ',', '');
        $dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
        $dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)

        $demonstrativo = explode("\n", $dadosboleto['demonstrativo']);
        for ($n=0; $n<=2; $n++)
        {
            $key = $n+1;
            $texto = isset($demonstrativo[$n]) ? $demonstrativo[$n] : '';
            $dadosboleto["demonstrativo{$key}"] = $texto;
        }

        $instrucoes = explode("\n", $dadosboleto['instrucoes']);
        for ($n=0; $n<=3; $n++)
        {
            $key = $n+1;
            $texto = isset($instrucoes[$n]) ? $instrucoes[$n] : '';
            $dadosboleto["instrucoes{$key}"] = $texto;
        }

        // DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
        $dadosboleto["quantidade"] = "";
        $dadosboleto["valor_unitario"] = "";
        $dadosboleto["aceite"] = "N";
        $dadosboleto["especie"] = "R$";
        $dadosboleto["especie_doc"] = "DM";


        // ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //

        // DADOS DA SUA CONTA - BANCO DO BRASIL
        $dadosboleto["agencia"] = "8888";
        $dadosboleto["conta"] = "88888888";

        // DADOS PERSONALIZADOS - BANCO DO BRASIL
        $dadosboleto["convenio"] = "888888";
        $dadosboleto["contrato"] = "888888";
        $dadosboleto["carteira"] = "88";
        $dadosboleto["variacao_carteira"] = "-019";

        // TIPO DO BOLETO
        $dadosboleto["formatacao_convenio"] = "7";
        $dadosboleto["formatacao_nosso_numero"] = "2";

        // SEUS DADOS
        $dadosboleto["identificacao"] = "Sacador";
        $dadosboleto["cpf_cnpj"] = "12.222.333/0001-24";
        $dadosboleto["endereco"] = "Av. Bento Gonçalves, 123. Bairro Centro - Cep 88.888-888";
        $dadosboleto["cidade_uf"] = "Porto Alegre - RS";
        $dadosboleto["cedente"] = "Empresa LTDA - ME";

        ob_start();
        if (!isset($_GET['print']) OR ($_GET['print'] !== '1'))
        {
            $url = $_SERVER['QUERY_STRING'];
            echo "<center> <a href='' onclick='window.open(\"engine.php?{$url}&print=1\")'> <h1>Clique aqui para Imprimir</h1></a> </center>";
        }

        include("app/lib/boleto/include/funcoes_bb.php");
        include("app/lib/boleto/include/layout_bb.php");
        if (isset($_GET['print']) AND ($_GET['print'] === '1'))
        {
            echo '<script>window.print();</script>';
        }
        $content = ob_get_clean();

        parent::add($content);
    }
}
?>