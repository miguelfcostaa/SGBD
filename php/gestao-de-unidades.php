<?php
$link = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if (!$link) {
    echo 'Connection failed';
    die("Connection failed: " . mysqli_connect_error());
}
else{
    echo 'Connected successfully';
}
if ( !is_user_logged_in() ) {
    die("Não tem autorização para aceder a esta página");
}
if (!current_user_can('manage_unit_types')){
    die("Não tem autorização para aceder a esta página");
}
echo "
<table>
  <tr>
    <th>id</th>
    <th>unidade</th>
    <th>subitem</th>
  </tr>";

$query1 = "SELECT * FROM subitem_unit_type ORDER BY subitem_unit_type.name ASC";
$result1 = mysqli_query($link,$query1);
while ($row = mysqli_fetch_assoc($result1) ) {
    echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td><td>";
    $z = $row["id"];
    $query2 = "SELECT subitem.name ,subitem.unit_type_id , subitem.item_id FROM subitem , subitem_unit_type WHERE subitem.unit_type_id = subitem_unit_type.id";
    $result2 = mysqli_query($link,$query2);
    while ($row = mysqli_fetch_assoc($result2) ) {
        if($z == $row["unit_type_id"]){
            echo $row["name"];
            $x = $row["item_id"];
            $query3 = "SELECT item.name , item.id FROM item WHERE item.id ";
            $result3 = mysqli_query($link,$query3);
            while($row = mysqli_fetch_assoc($result3)) {
                if($x==$row["id"]){
                    echo ' (' . $row["name"] . '), ';
                }
            }
        }
    }
    echo"</td></tr>";
}
echo"</table>";

$nome = trim($_POST["nome"]);

if ((!isset($_POST['inserir']) ) || isset($_POST['voltar'])){
    echo"<h3>Gestão de unidades - introdução</h3>
        <form action='' method='POST'>
        <p>Nome: <input type='text' name='nome'></p>
        <input name='inserir' type=hidden value='inserir'>
        <p>Inserir tipo de unidade: <input type='submit' name='inserir' value='submit'/></p>    
        </form>";
}

elseif(isset($_POST['inserir'])){
    echo"<h3>Gestão de unidades - inserção</h3>";

    $erro = false;

    if (empty($nome)) {
        $erro = true;
        echo "<strong>Nome do subitem: </strong>Introduza um nome válido.<br>";
    }
    if ($nome != ''){
        $query4 = "INSERT INTO subitem_unit_type (name) VALUE ('$nome')";
        $result4 = mysqli_query($link, $query4);
        echo "<h3>Uma unidade nova foi introduzida com sucesso!</h3>";
    }
    else{
        echo "<h3>Não foi possivel introduzir a nova unidade</h3>";
    }
}
echo "<script type='text/javascript'>document.write(\"<a href='javascript:history.back()' class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>\");</script>
<noscript>
<a href='".$_SERVER['HTTP_REFERER']."‘ class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>
</noscript>";
?>