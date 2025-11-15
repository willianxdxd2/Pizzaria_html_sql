<?php

    include_once("conn.php");
    $method = $_SERVER["REQUEST_METHOD"];

        //resgate dos dados, montagem do pedido
    if ($method === "GET"){

        $bordasQuery = $conn -> query("SELECT * FROM bordas;");

        $bordas = $bordasQuery -> fetchAll();

        $massasQuery = $conn -> query("SELECT * FROM massas;");

        $massas = $massasQuery -> fetchAll();

        $saboresQuery = $conn -> query("SELECT * FROM sabores;");

        $sabores = $saboresQuery -> fetchAll();
        

       //criação do pedido 
    }else if($method === "POST"){
        $data = $_POST;
        $borda = $data ["borda"];
        $massa = $data ["massa"];
        $sabores= $data ["sabores"];


        if(count($sabores) > 3){
            $_SESSION["msg"] = "Selecione no máximo 3 sabores!";
            $_SESSION["status"] = "warning";
        }
        else{

            $variavel = $conn ->prepare("INSERT INTO pizzas(borda_id,massa_Id)VALUES(:borda,:massa)");

            $variavel ->bindParam(":borda",$borda,PDO::PARAM_INT);
            $variavel ->bindParam(":massa",$borda,pdo::PARAM_INT);

            $variavel->execute();

            $pizzaId = $conn -> lastInsertId();

            $variavel = $conn -> prepare("INSERT INTO pizza_sabor(pizza_id,sabor_id)VALUES (:pizza,:sabor)");

            foreach($sabores as $sabor){
                $variavel -> bindParam(":pizza",$pizzaId,PDO::PARAM_INT);
                $variavel -> bindParam(":sabor",$sabor,PDO::PARAM_INT);

                $variavel -> execute();
            }

            //criar o pedido da pizza
            $variavel = $conn ->prepare("INSERT INTO pedidos (pizza_id,status_id)VALUES(:pizza,:status)");
            $statusId = 1;
            //filtrar inputs
            $variavel -> bindParam(":pizza",$pizzaId);
            $variavel -> bindParam(":status",$statusId);

            $variavel -> execute();

            //Exibir mensagem de sucesso
            $_SESSION["msg"] = "Pedido realizado com sucesso";
            $_SESSION["status"] = "sucess";

        }
        //retorna para pagina inicial
        header("Location: ..");
    }

    
?>