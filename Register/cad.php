<style>
    :root{
        --cor1: #424769;
        --cor2: #ffffff;
        --cor3: #ff0000;
        --cor4: #0af502;
        --cor5: #dac50d;
    }

    body{
        background-color: var(--cor1);
    }

    #resgitrado{
        color: var(--cor4);
    }
    .erro{
        background-color: var(--cor2);
        width: 390px;
        height: 40px;
        border-radius: 20px;
        margin: 165px auto 0px auto;
        display: flex;
        align-items: center;      
        justify-content: center;
        padding: 0px;
    }
    p{
        margin: 0px auto 18px auto ;
        color:var(--cor3)
    }
</style>


<?php

    //recebe dados do html
        
        $nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : "";
        $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
        $dados_senha = isset($_POST["senha"]) ? trim($_POST["senha"]) : "";
        $dados_senha_2 = isset($_POST["csenha"]) ? trim($_POST["csenha"]) : "";
   
    // fim recebe dados 

    //Conexao Banco

        $dbname = "NOVO_BANCO";
        $host = "127.0.0.1";
        $name = "root";
        $senha = "222";
        
        try {
            $pdo= new PDO("mysql:dbname=$dbname;host=$host",$name,$senha);
        } catch (PDOException $e) {
            echo "ERRO BANCO!";
        }catch(Exception $e){
            echo "ERRO GENERICO!";
        }
    
    //fim conexao banco

    //valida dados

        function ValidarDados($nome,$email,$dados_senha,$dados_senha_2,$pdo){

            #Validaçoes fora do banco
            if (empty($nome) || empty($email) || empty($dados_senha) || empty($dados_senha_2)) {
                return '<div class="erro"><p><br>Campo em branco !"</p></div>';
                
            }
            if (!filter_var($email,FILTER_VALIDATE_EMAIL)){
                return '<div class="erro"><p><br>Email invalido !"</p></div>';
                
            }
            if($dados_senha !== $dados_senha_2){
                
                return '<div class="erro"><p><br>Senhas nao sao iguais !"</p></div>';  
                
            }
            if (strlen($dados_senha) < 8) {
                return '<div class="erro"><p><br>Senha nao tem 8 digitos !"</p></div>';
                
            }
            if (!preg_match('/[\W]/', $dados_senha)) { 
                return '<div class="erro"><p><br>Senha deve conter pelo menos 1 caractere especial !."</p></div>';
                
            }

            //Retornando null
            return null;
            
        }

        $erro=ValidarDados($nome,$email,$dados_senha,$dados_senha_2,$pdo);
        if ($erro !== null) {
            echo $erro;
            
        }

        function validaPDO($nome,$email,$pdo){

            if (!empty($nome)) {

                //Nome
                    $valida= $pdo->prepare("SELECT nome FROM pessoas WHERE nome=:a ");
                    $valida->bindValue(":a",$nome);
                    $valida->execute();

                    if ($valida->rowCount() > 0) {
                        return '<div class="erro"><p><br>Nome já existe !"</p></div>';
                        
                        
                    }   
                //fim nome    
            }

            if (!empty($email)) {

                //Email
                    $valida= $pdo->prepare("SELECT email FROM pessoas WHERE email=:b ");
                    $valida->bindValue(":b",$email);
                    $valida->execute();

                    if ($valida->rowCount() > 0) {
                        return '<div class="erro"><p><br>Email já existe !"</p></div>';
                    }   
                //fim email  
            }

            //Retornando null
            return null;
        }

        $erro_pdo=validaPDO($nome,$email,$pdo);
        if ($erro_pdo !== null) {
            echo $erro_pdo;
            
        }

    //fim valida

    //Tranformando senha 
    $hash_senha=password_hash($dados_senha,PASSWORD_DEFAULT);

    //Salvando no banco mysql

    if ($erro == null && $erro_pdo == null){
        $salvando=$pdo->prepare("INSERT INTO pessoas(nome,email,senha) VALUES(:c,:d,:e) ");
        $salvando->bindValue(":c",$nome);
        $salvando->bindValue(":d",$email);
        $salvando->bindValue(":e",$hash_senha);
        $salvando->execute();

        echo '<div class="erro"><p id="resgitrado"><br>Registrado com secesso</p></div>'; 
    }

?>