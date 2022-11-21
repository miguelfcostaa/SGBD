<?php
$link = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}
else{
    echo 'Connected successfully!';
}

if ( !is_user_logged_in() ) {
    die("Não tem autorização para aceder a esta página");
}
if (!current_user_can('manage_allowed_values')){
    die("Não tem autorização para aceder a esta página!");
}

echo"
<table>
    <tr>
    <th>Item</th>
    <th>id</th>
    <th>subitem</th>
    <th>id</th>
    <th>valores permitidos</th>
    <th>estado</th>
    <th>ação</th>
</tr>";


function numrow($n, $link){
    $num = 0;
    $query01 = "SELECT item.name, subitem.id,subitem.name FROM item, subitem WHERE item.id=$n and item.id=subitem.item_id";
    $result01 = mysqli_query($link, $query01);

    while ($row=mysqli_fetch_assoc($result01)){
        $asd=$row["id"];
        $num = $num + 1;

        $query02="SELECT DISTINCT subitem.id,subitem_allowed_value.subitem_id FROM subitem , subitem_allowed_value WHERE  subitem.id=$asd and subitem.id=subitem_allowed_value.subitem_id";
        $result02=mysqli_query($link,$query02);
        while($row=mysqli_fetch_assoc($result02)){
            $num=$num-1;
        }

        $query0 = "SELECT item.name,subitem.name,subitem_allowed_value.subitem_id,subitem_allowed_value.value FROM item, subitem, subitem_allowed_value WHERE item.id=$n and subitem.item_id=item.id and subitem.id=subitem_allowed_value.subitem_id";
        $result0 = mysqli_query($link, $query0);
        while ($row = mysqli_fetch_assoc($result0)) {
            $dsa=$row["subitem_id"];
            if($asd == $dsa){
                $num = $num + 1;
            }
        }
    }
    return $num;
}

function numrow2($asd, $link){
    $num = 0;

    $query0 = "SELECT subitem.name,subitem_allowed_value.subitem_id,subitem_allowed_value.value FROM subitem, subitem_allowed_value WHERE subitem.id=$asd and subitem.id=subitem_allowed_value.subitem_id";
    $result0 = mysqli_query($link, $query0);
    while ($row = mysqli_fetch_assoc($result0)) {
        $dsa=$row["subitem_id"];
        if($asd == $dsa){
            $num = $num + 1;
        }
    }
    return $num;
}

$query1 = "SELECT item.name, item.id FROM item ORDER BY item.name ASC;";
$result1 = mysqli_query($link,$query1);
while($row = mysqli_fetch_assoc($result1)) {
    $x = $row['id'];
    $linha = numrow($x, $link);
    if($linha ==0){
        $linha=1;
        echo "<tr><td rowspan ='$linha'>" . $row["name"] ."</td>";
        echo "<td>Não há subitens definidos, nem há valores permitidos definidos</td></tr>";
    }
    else{
        echo "<tr><td rowspan ='$linha'>" . $row["name"] ."</td>";

        $query2 = "SELECT subitem.id, subitem.name , subitem.item_id FROM subitem";
        $result2= mysqli_query($link,$query2);
        while($row =mysqli_fetch_assoc($result2)){
            $c=$row["id"];
            $linha2 = numrow2($c,$link);
            if($x==$row["item_id"]){
                if($linha2 == 0 ){
                    $linha2=1;
                    echo "<td rowspan ='$linha2'>".$row["id"]."</td><td rowspan ='$linha2'>". $row['name'] . "</td>";
                    echo "<td>Não há valores permitidos definidos</td></tr>";
                }
                else{
                    echo "<td rowspan ='$linha2'>".$row["id"]."</td><td rowspan ='$linha2'>". $row['name'] . "</td>";
                    $query3 = "SELECT subitem_allowed_value.id, subitem_allowed_value.value, subitem_allowed_value.state , subitem_allowed_value.subitem_id FROM subitem_allowed_value";
                    $result3 = mysqli_query($link,$query3);
                    while ($row =mysqli_fetch_assoc($result3)){
                        if($c == $row['subitem_id']) {
                            echo "<td>" . $row['id'] . "</td>
                          <td>" . $row['value'] . "</td>
                          <td>" . $row['state'] . "</td></tr>";
                        }
                    }
                }
            }
        }
    }

    echo "</tr>";
}
echo "</table>";

$nome = trim($_POST['nome']);
$subitemid = trim($_POST["subitem_id"]);

if(!isset($_POST['insert']) && !isset($_POST['voltar'])) {
    $query60 = "SELECT DISTINCT subitem_allowed_value.subitem_id FROM subitem_allowed_value";
    $result60 = mysqli_query($link, $query60);
    $array5 = array();
    echo "<strong>Gestão de Valores permitidos - Introdução</strong>";
    foreach ($result60 as $subitemid) {
        $array5[] = $subitemid["subitem_id"];
    }
    echo '<br><label for= item_type> Tipo - subitem id (Obrigatório):</lable>';
    for ($i = 0; $i < sizeof($array5); $i++) {
        echo "<br><input type='radio' name='subitemid' value='$array5[$i]'> $array5[$i]";
    }


    echo '<body>
<form action="" method="post">
 Novo Valor permitido (Obrigatório):
    <input type="text" name="nome"><br>
    <input type="hidden" name="insert" value="insert">    
    <br><input type="submit" name="submit" value="inserir valores na tabela">
        
</form>';

}


else {
    if (isset($_POST["insert"])) {
        echo "<strong>Gestão de Valores Permitidos - inserção<br></strong>";
        $erro = false;
        if (empty($nome)) {
            $erro = true;
            echo "Falta o nome!<br>";
        }
    }

    if (isset($_POST['insert']) && $erro == false) {

        echo "<ul> Confirmar os dados a serem introduzidos: <br>  
                        <li> Nome: '$nome'</li><br>
                        
              </ul>";

        echo '<form action="" method="post">        
        <input type="hidden" name="nome" value="' . $nome . '">         
        </form>
        ';
    }
    if (isset($_POST["nome"])) {

        $query8 = "INSERT INTO subitem_allowed_value (subitem_id, value) VALUES ('$subitemid','$nome')";
        $result8 = mysqli_query($link, $query8);
        //echo "Dados inseridos com sucesso!";
        echo '
             <form action="" method="post"> 
             <input type="hidden" name="inicio" value="inicio">
             <input type="submit" name="submit" value="continuar"></form>';

    }


}
echo "<script type='text/javascript'>document.write(\"<a href='javascript:history.back()' class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>\");</script>
<noscript>
<a href='" . $_SERVER['HTTP_REFERER'] . "‘ class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>
</noscript>";

?>