<?php
$result = "";

if(isset($_POST['display'])){
    $result = $_POST['display'];
}

if(isset($_POST['btn'])){
    $btn = $_POST['btn'];
    if($btn == "C"){
        $result = "";
    } elseif($btn == "="){
        // حساب العملية
        try {
            $result = eval("return $result;");
        } catch (Exception $e) {
            $result = "Erreur";
        }
    } else {
        $result .= $btn;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calculatrice</title>
    <style>
        body{
            background: #afc1ddff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial;
        }
        .calc{
            background: #3b4655ff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 0 20px #817b7bff;
        }
        #display{
            width: 94%;
            height: 60px;
            font-size: 25px;
            border: none;
            margin-bottom: 15px;
            border-radius: 10px;
            text-align: right;
            padding: 10px;
            background: #21262d;
            color: #fff;
        }
        .btn{
            width: 70px;
            height: 60px;
            margin: 5px;
            font-size: 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            background: #30363d;
            color: #fff;
            transition: 0.2s;
        }
        .btn:hover{
            background: #484f58;
        }
        .operator{
            background: #1f6feb;
        }
        .operator:hover{
            background: #388bfd;
        }
        .equal{
            background: #238636;
        }
        .equal:hover{
            background: #2ea043;
        }
        .clear{
            background: #da3633;
        }
        .clear:hover{
            background: #f85149;
        }
    </style>
</head>
<body>

<div class="calc">
    <form method="POST">
        <input type="text" id="display" name="display" value="<?= $result ?>" readonly>

        <div>
            <button class="btn clear" name="btn" value="C">C</button>
            <button class="btn operator" name="btn" value="/">/</button>
            <button class="btn operator" name="btn" value="*">*</button>
            <button class="btn operator" name="btn" value="-">-</button>
        </div>

        <div>
            <button class="btn" name="btn" value="7">7</button>
            <button class="btn" name="btn" value="8">8</button>
            <button class="btn" name="btn" value="9">9</button>
            <button class="btn operator" name="btn" value="+">+</button>
        </div>

        <div>
            <button class="btn" name="btn" value="4">4</button>
            <button class="btn" name="btn" value="5">5</button>
            <button class="btn" name="btn" value="6">6</button>
            <button class="btn operator" name="btn" value="(">(</button>
        </div>

        <div>
            <button class="btn" name="btn" value="1">1</button>
            <button class="btn" name="btn" value="2">2</button>
            <button class="btn" name="btn" value="3">3</button>
            <button class="btn operator" name="btn" value=")">)</button>
        </div>

        <div>
            <button class="btn" name="btn" value="0">0</button>
            <button class="btn" name="btn" value=".">.</button>
            <button class="btn equal" name="btn" value="=">=</button>
        </div>
    </form>
</div>

</body>
</html>
