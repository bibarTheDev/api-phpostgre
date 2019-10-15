<?php

function connect($config)
{
    $url = "host=".$config["host"]." port=".$config["port"]." dbname=".$config["db"]." user=".$config["user"]." password=".$config["pass"];
    $conn = pg_connect($url);

    if(!$conn){
        echo json_encode([
            "erro" => "conecxao falhou",
            "dados" => $config,
            "url" => $url
        ]);
        exit;
    }

    return $conn;
}

function returnQuery($query, $config)
{
    $conn = connect($config);
    $resp = pg_query($conn, $query);

    if(pg_result_error($resp) != ""){
        echo json_encode(["erro" => pg_result_error($resp)]);
        return;    
    }
    echo json_encode([
        "erro" => "",
        "linhas-retornadas" => pg_num_rows($resp),
        "dados" => pg_fetch_all($resp)
    ]);
}

function noReturnQuery($query, $config)
{
    $conn = connect($config);
    $resp = pg_query($conn, $query);

    if(pg_result_error($resp) != ""){
        echo json_encode(["erro" => pg_result_error($resp)]);
        return;    
    }
    echo json_encode([
        "erro" => "",
        "linhas-afetadas" => pg_affected_rows($resp)
    ]);
}

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

$config = [
    "host" => "",
    "port" => "",
    "db" => "",
    "user" => "",
    "pass" => ""
];

if(isset($_GET["sql"])){
    returnQuery($_GET["sql"], $config);
}
else if(isset($_POST["sql"])){
    noReturnQuery($_POST["sql"], $config);
}
else{
    echo json_encode(["erro" => "requisicao falhou"]);
}