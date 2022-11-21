<?php

$link = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}
else{
    echo 'Connected successfully';
}

if ( !is_user_logged_in() ) {
    die("Não tem autorização para aceder a esta página");
}
if (!current_user_can('manage_items')){
    die("Não tem autorização para aceder a esta página!");
}


echo "
<table>
<tbody>
  <tr>
    <th>Tipo de Item</td>
    <th>id</th>
    <th>Nome do Item</th>
    <th>Estado</th>
    <th>Açao</th>
  </tr>";

function numrow($n, $link)
{
    $query0 = "SELECT item.id FROM item WHERE item.item_type_id= $n ";
    $result0 = mysqli_query($link, $query0);
    $num = 0;
    while ($row = mysqli_fetch_assoc($result0)) {
        $num = $num + 1;
    }
    if ($num == 0) {
        return $num + 1;
    } else {
        return $num;
    }
}


$query = "SELECT item_type.id,item_type.name FROM item_type ";
$result = mysqli_query($link, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $x = $row['id'];
    $please = numrow($x, $link);
    echo "<tr><td rowspan='$please'>" . $row["name"] . "</td>";
    $query3 = "SELECT item.id,item.name,item.state, item.item_type_id FROM item";
    $result3 = mysqli_query($link, $query3);
    while ($row2 = mysqli_fetch_assoc($result3)) {
        if ($x == $row2["item_type_id"])
            echo "<td>" . $row2["id"] . "</td>
              <td>" . $row2["name"] . "</td>
              <td>" . $row2["state"] . "</td>
              <td>" . ($row2["state"] == "active" ? "[editar][desativar]" : "[editar][ativar]") . "</td></tr>";

    }
}
echo "</table>";


$nome = trim($_POST['nome']);
$itemtype = trim($_POST['item_type']);
$state = trim($_POST['state']);

if(!isset($_POST['insert']) && !isset($_POST['voltar'])) {
    echo "<strong>Gestão de Itens - Introdução</strong>";

    echo '<body>
<form action="" method="post">
    Nome: <input type="text" name="nome"><br>';


//-----------------------------------------------------------------------
    $query6 = "SELECT DISTINCT item_type.name FROM item_type";
    $result6 = mysqli_query($link, $query6);
    $array = array();
    foreach ($result6 as $item_type) {
        $array[] = $item_type["name"];
    }
    echo '<br><label for= item_type> Tipo (Obrigatório):</lable>';
    for ($i = 0; $i < sizeof($array); $i++) {
        echo "<br><input type='radio' name='item_type' value='$array[$i]'> $array[$i]";
    }

//-----------------------------------------------------------------------
    $query7 = "SELECT DISTINCT item.state FROM item";
    $result7 = mysqli_query($link, $query7);
    $array2 = array();
    foreach ($result7 as $item) {
        $array2[] = $item["state"];
    }

    echo '<body>
<br><label for=item > Estado (Obrigatório):</label>';
    for ($i = 0; $i < sizeof($array2); $i++) {
        echo "<br><input type='radio' name='state' value='$array2[$i]'>$array2[$i]";
    }
//-----------------------------------------------------------------------
    echo '
    <input type="hidden" name="insert" value="inserir">
    <br><input type="submit" name="submit" value="inserir valores na tabela">

</form>';
}
//------------------server-side-----------------
else{
    if(isset($_POST["insert"])) {
        echo "<strong>Gestão de Itens - inserção<br></strong>";
        $erro = false;
        if (empty($nome)) {
            $erro = true;
            echo "Falta o nome!<br>";
        }
        if (empty($itemtype)) {
            $erro = true;
            echo "Falta o tipo!<br>";
        }
        if (empty($state)) {
            $erro = true;
            echo "Falta o estado!";
        }
    }
    if(isset($_POST['insert'])&& $erro==false){

        echo "<ul> Confirmar os dados a serem introduzidos: <br>  
                        <li> Nome: '$nome'</li><br>
                        <li> Tipo de Item: '$itemtype'</li><br>
                        <li> Estado: '$state'</li><br>
              </ul>";

        echo'<form action="" method="post">
        <input type="hidden" name="voltar" value="voltar">
        <input type="submit" name="submit" value="continuar">
        <input type="hidden" name="nome" value="'.$nome.'">
        <input type="hidden" name="item_type" value="'.$itemtype.'">
        <input type="hidden" name="state" value="'.$state.'">
        
        </form>
        ';
    }


    if(isset($_POST["voltar"])){
        $verify_item_id = "SELECT item_type.name,item_type.id FROM item_type";
        $result_a = mysqli_query($link,$verify_item_id);
        while ($row = mysqli_fetch_assoc($result_a)) {
            if ($row['name'] == $itemtype) {
                $i = $row['id'];
            }
        }
        echo"Dados inseridos com sucesso!";
        $query8="INSERT INTO item (name, item_type_id, state) VALUES ('$nome', '$i','$state')";
        $result8=mysqli_query($link,$query8);
        echo'<input type="hidden" name="inicio" value="voltar">
             <input type="submit" name="submit" value="continuar">';

    }
    if(isset($_POST["inicio"])){

    }

}
echo "<script type='text/javascript'>document.write(\"<a href='javascript:history.back()' class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>\");</script>
<noscript>
<a href='".$_SERVER['HTTP_REFERER']."‘ class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>
</noscript>";
?>