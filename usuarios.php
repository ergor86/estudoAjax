<?php
    define('SEPARADOR_CSV', ',');
    define('ARQUIVO', 'usuarios.csv');

    $metodo = $_SERVER['REQUEST_METHOD'];
    call_user_func('on'.$metodo);

    function onGET(){
        header('Content-Type:application/json');
        $conteudo = file_get_contents(ARQUIVO);
        $linhas = explode(PHP_EOL, $conteudo);
        array_pop($linhas); //remove ultima linha do arquivo que o calc adiciona automaticamente.
        $contatos = array();
        foreach($linhas as $linha){
            $arrayContato = explode(SEPARADOR_CSV, $linha);
            $contato = array('nome' => $arrayContato[0], 'email' => $arrayContato[1]);
            array_push($contatos, $contato);
        }
        echo json_encode($contatos);
    } 
    
    function onPOST(){
        header('Content-Type:application/json');
        $erros = array();
        if(!isset($_POST['nome']) || mb_strlen($_POST['nome'])==0){
            array_push($erros, 'Por favor, insira o nome.');
        }else{
            $nome = $_POST['nome'];
            $tam = mb_strlen($nome);
            if($tam < 2 || $tam >60){
                array_push($erros, "Insira um nome com no minimo 2 e no maximo 60 caracteres.");
            }
        }

        if(!isset($_POST['email']) || mb_strlen($_POST['email']==0)){
            array_push($erros, 'Por favor, insira o e-mail');
        }else{
            $email = $_POST['email'];
            $posicaoArroba = mb_strpos($email, '@');
            if($posicaoArroba === false || $posicaoArroba < 1){
                array_push($erros, 'Insira um e-mail valido. Precisa conter @.');
            }
        }
        if(!isset($_POST['senha']) || mb_strlen($_POST['senha'])==0){
            array_push($erros, "Por favor, insira a senha.");
        }else{
            $senha = $_POST['senha'];
            $tam = mb_strlen($senha);
            if($tam < 6 || $tam > 100){
                array_push($erros, 'Insira uma senha com no minimo 6 e no maximo 100 caracteres');
            }
        }

        if(count($erros)>0){
            header($_SERVER['SERVER_PROTOCOL']. '400');
            echo json_encode($erros);
        }else{
            $conteudo = file_get_contents(ARQUIVO)."$nome,$email,$senha".PHP_EOL;
		file_put_contents(ARQUIVO, $conteudo);
		echo "{}";
        }

    }
?>