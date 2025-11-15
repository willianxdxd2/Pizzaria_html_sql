<?php

    include_once("conn.php");

    $method = $_SERVER["REQUEST_METHOD"];

    if($method == "GET"){

        $pedidosQuery = $conn->query("SELECT * FROM pedidos;");

        $pedidos = $pedidosQuery ->fetchAll();

        $pizzas = [];

        foreach($pedidos as $pedido){

            $pizza = [];

            $pizza["id"] = $pedido["pizza_id"];

            $pizzaQuery = $conn -> prepare("SELECT * FROM pizzas WHERE id = :pizza_id");

            $pizzaQuery ->bindParam(":pizza_id",$pizza["id"]);

            $pizzaQuery ->execute();

            $pizzaData = $pizzaQuery -> fetch(PDO::FETCH_ASSOC);

            //resgatando borda

            $bordaQuery = $conn -> prepare("SELECT * FROM bordas WHERE id = :borda_id");
            $bordaQuery ->bindParam(":borda_id",$pizzaData["borda_id"]);

            $bordaQuery ->execute();

            $bordaData = $bordaQuery -> fetch(PDO::FETCH_ASSOC);

            $pizza["borda"] = $bordaData["tipo"];

            //resgatando massa

            $massaQuery = $conn -> prepare("SELECT * FROM massas WHERE id = :massa_id");
            $massaQuery ->bindParam(":massa_id",$pizzaData["massa_id"]);

            $massaQuery ->execute();

            $massaData = $massaQuery -> fetch(PDO::FETCH_ASSOC);

            $pizza["massa"] = $massaData["tipo"];

            //resgatando os sabores da pizza
            $saboresQuery = $conn -> prepare("SELECT * FROM sabores WHERE id = :sabores_id");
            $saboresQuery ->bindParam(":sabores_id",$pizzaData["sabores_id"]);

            $saboresQuery ->execute();

            $saboresData = $saboresQuery -> fetchAll(PDO::FETCH_ASSOC);


            $saboresDaPizza = [];
            foreach($saboresData as $sabor){
                array_push($saboresDaPizza, $sabor ["nome"]);
            }
            $pizza["sabores"] = $saboresDaPizza;

            //resgatando o nome dos sabores
            $saboresDaPizza = [];
            $saborQuery = $conn->prepare("SELECT * FROM sabores WHERE id = :sabor_id");

            foreach ($saboresData as $sabor){
                $saborQuery ->bindParam(":sabor_id",$sabor["sabor_id"]);
                $saborQuery ->execute();
                $saborPizza = $saborQuery->fetch(PDO::FETCH_ASSOC);
                array_push($saboresDaPizza,$saborPizza["nome"]);
            }
            $pizza["sabores"] = $saboresDaPizza;

            //adiciona status do pedido
            $pizza["status"] = $pedido["status_id"];

            //adiciona o array de pizza, ao array das pizzas
            array_push($pizzas,$pizza);
        }

        //resgatando status

        $statusQuery = $conn ->query("SELECT * FROM status");
        $status = $statusQuery -> fetchAll();
    }else if ($method === "POST"){
            // verificaidnod tipo de post
        $type = $_POST["type"];


        //deletar pedido
        if($type === "delete"){

            $pizzaId = $_POST["id"];

            $deleteQuery = $conn ->prepare("DELETE FROM pedidos WHERE pizza_id = :pizza_id");

            $deleteQuery -> bindParam(":pizza_id",$pizzaId,PDO::PARAM_INT);

            $deleteQuery -> execute();

            $_SESSION["msg"] = "Pedido removido com sucesso!";
            $_SESSION["status"] = "sucess";
            //atualizar status do pedido
        }else if($type === "update"){

            $pizzaId = $_POST["id"];
            $statusId = $_POST["status"];

            $updateQuery = $conn -> prepare("UPDATE pedidos SET status_id = :status_id WHERE pizza_id = :pizza_id");
            $updateQuery -> bindParam(":pizza_id",$pizzaId, PDO::PARAM_INT);
            $updateQuery -> bindParam(":status_id",$statusId, PDO::PARAM_INT);

            $updateQuery ->execute();

            $_SESSION["msg"] = "Pedido removido com sucesso!";
            $_SESSION["status"] = "sucess";


        }
        //retorna usuário para dashboard

        header("Location: ../dashboard.php");




    }
?>