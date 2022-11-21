<?php
require_once("custom/php/common.php");

$link = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if (!$link) {
    echo "<br> Connection failed!";
    die("Connection failed: " . mysqli_connect_error());
}
else {
    //echo 'Connected successfully';
}

if ( !is_user_logged_in() ) {
    die("<br> Não tem autorização para aceder a esta página");
}
if (!current_user_can('manage_records')){
    die("<br> Não tem autorização para aceder a esta página");
}


function numrow($n, $link) {
    $query0 = "SELECT subitem.item_id FROM subitem WHERE item_id = $n ";
    $result0 = mysqli_query($link, $query0);
    $count = mysqli_num_rows($result0);
    if ($count == 0) {
        $count = 1;
    }
    return $count;
}

$nome = trim($_POST["nome"]);
$value_type = trim($_POST["value_type"]);
$itens = trim($_POST["itens"]);
$ordem_form = trim($_POST["ordem_form"]);
$rname = $_POST['rname'];
$type_form = trim($_POST['type_form']);
$subitem_unit_type = trim($_POST['subitem_unit_type']);

if (!isset($_POST['inserir']) && !isset($_POST['continuar'])){
    $query = "SELECT * FROM subitem";
    $result = mysqli_query($link,$query);
    if(sizeof($result) == 0){
        echo "<strong>Não há subitens especificados.</strong>";
    }
    else {
        echo "<table>
            <tr>
            <th>item</th>
            <th>id</th>
            <th>subitem</th>
            <th>tipo de valor</th>
            <th>nome do campo no formulário</th>
            <th>tipo do campo no formulário</th>
            <th>tipo de unidade</th>
            <th>ordem do campo no formulário</th>
            <th>obrigatório</th>
            <th>estado</th>
            <th>ação</th>
        </tr>";
    }

    $query1 = "SELECT item.id, item.name FROM item ORDER BY item.name ASC";
    $result1 = mysqli_query($link,$query1);
    while ($row = mysqli_fetch_assoc($result1)) {
        $x = $row['id'];
        $please=numrow($x,$link);
        echo "<tr><td rowspan ='$please'>".$row["name"] ."</td>";
        $query2 = "SELECT subitem.item_id,subitem.unit_type_id, subitem.id, subitem.name, subitem.value_type, subitem.form_field_name, subitem.form_field_type FROM item,subitem WHERE subitem.item_id=item.id ORDER BY subitem.name ASC";
        $result2 = mysqli_query($link,$query2);
        while ($row2 = mysqli_fetch_assoc($result2)) {
            if ($x == $row2["item_id"]){
                $c = $row2["name"];
                $z = $row2["unit_type_id"];
                echo "<td>".$row2['id']."</td><td>".$row2['name']."</td><td>".$row2['value_type']."</td><td>".$row2['form_field_name']."</td><td>".$row2['form_field_type']."</td>";
                $query3 = "SELECT DISTINCT subitem_unit_type.name , subitem_unit_type.id FROM subitem_unit_type,subitem WHERE subitem.unit_type_id=subitem_unit_type.id";
                $result3 = mysqli_query($link,$query3);
                $query4 = "SELECT  DISTINCT subitem.name , subitem.form_field_order,subitem.mandatory,subitem.state FROM subitem";
                $result4 = mysqli_query($link,$query4);
                while ($row3 = mysqli_fetch_assoc($result3)) {
                    if($z == $row3["id"] || $z == null){
                        if ($z == null) {
                            echo "<td>"."-"."</td>";
                            while ($row4 = mysqli_fetch_assoc($result4)){
                                if($c == $row4["name"]){
                                    if($row4['mandatory']==0){
                                        echo '<td>'.$row4['form_field_order']."</td><td>".'não'."</td><td>".$row4['state']."</td>";

                                    }
                                    else{
                                        echo '<td>'.$row4['form_field_order']."</td><td>".'sim'."</td><td>".$row4['state']."</td>";
                                    }
                                    if ($row4['state'] == "active"){
                                        echo "<td>"."[editar] [desativar]"."</td></tr>";
                                    }
                                    if ($row4['state'] == "inactive") {
                                        echo "<td>"."[editar] [ativar]"."</td></tr>";
                                    }
                                }
                                $w = false;
                            }
                            break;
                        }
                        else {
                            echo '<td>'.$row3['name']."</td>";
                        }
                        while ($row4 = mysqli_fetch_assoc($result4)){
                            if($c == $row4["name"]){
                                if($row4['mandatory']==0){
                                    echo '<td>'.$row4['form_field_order']."</td><td>".'não'."</td><td>".$row4['state']."</td>";
                                }
                                else{
                                    echo '<td>'.$row4['form_field_order']."</td><td>".'sim'."</td><td>".$row4['state']."</td>";
                                }
                                if ($row4['state'] == "active"){
                                    echo "<td>"."[editar] [desativar]"."</td></tr>";
                                }
                                if ($row4['state'] == "inactive") {
                                    echo "<td>"."[editar] [ativar]"."</td></tr>";
                                }
                            }
                            $w = false;
                        }
                    }
                }
            }
        }
        echo "</tr>";
    }
    echo "</table>";

    $query5 = "SELECT DISTINCT subitem.value_type FROM subitem";
    $result5 = mysqli_query($link,$query5);
    $array = array();
    foreach ($result5 as $valuetype){
        $array[] = $valuetype['value_type'];
    }
    $query6 = "SELECT DISTINCT item.name FROM item";
    $result6 = mysqli_query($link,$query6);
    $array2 = array();
    foreach ($result6 as $item){
        $array2[] = $item['name'];
    }
    $query7 = "SELECT DISTINCT subitem.form_field_type FROM subitem";
    $result7 = mysqli_query($link,$query7);
    $array3 = array();
    foreach ($result7 as $typeform){
        $array3[] = $typeform['form_field_type'];
    }
    $query8 = "SELECT DISTINCT subitem_unit_type.name FROM subitem_unit_type";
    $result8 = mysqli_query($link,$query8);
    $array4 = array();
    foreach ($result8 as $subitem_type){
        $array4[] = $subitem_type['name'];
    }
    $obrigatorio = 0;

    echo "<h3>Gestão de subitems - introdução</h3>

        <form action='' method='post'>
        <label for=nome>Nome do subitem - (obrigatório)</label>
        <input type=text id=nome name=nome /> <br>
        
        <label for=value_type>Tipo de valor - (obrigatório)</label>";
        for ($i=0;$i < sizeof($array); $i++) {
            echo "<br><input type=radio name='value_type' value='$array[$i]'/> $array[$i]";
        }

        echo "<p>      </p><br>
        <label for=itens>Item - (obrigatório)</label><select name='itens'><option value=' '>  </option>";
        for ($i=0;$i < sizeof($array2); $i++) {
            echo "<br><option value='$array2[$i]'> $array2[$i] </option>";
        }

        echo "</select><p>      </p><br>
        <label for=type_form>Tipo do campo do formulário - (obrigatório)</label>";
        for ($i=0;$i < sizeof($array3); $i++) {
            echo "<br><input type=radio name='type_form' value='$array3[$i]'/> $array3[$i]";
        }

        echo "<p>      </p>
        <br><label for=subitem_unit_type>Tipo de unidade - (opcional)</label><select name='subitem_unit_type'><option value='null'>null</option>";
        for ($i=0;$i < sizeof($array4); $i++) {
            echo "<br><option value='$array4[$i]'> $array4[$i] </option>";
        }

        echo "</select><p>      </p>
        <br><label for=ordem_form>Ordem do campo no formulário - (obrigatório e um número superior a 0)</label>
        <input type=text id=ordem_form name=ordem_form /> <br> 
        
        <label for=obrigatorio>Obrigatório - (obrigatório)</label>
        <input type=radio name='rname' value='yes'><label> Sim</label>
        <input type=radio name='rname' value='no'><label> Não</label>
        
        <input type='hidden' name='inserir' value='inserir'>
        <input type='submit' name='submit' value='submit'>
    </form>";
}
else {
    if (isset($_POST['inserir'])) {
        echo "<h3>Gestão de subitens - inserção</h3>";

        $erro = false;

        if(empty($nome)){
            $erro = true;
            echo "<strong>Nome do subitem: </strong>Introduza um nome válido.<br>";
        }
        else {
            if(!preg_match("~^\p{L}+(?:[-\h']\p{L}+)*$~u", $nome)) {
                $erro = true;
                echo "<strong>Nome: </strong>Introduza apenas letras e espaços.<br>";
            }
        }
        if (empty($value_type)) {
            $erro = true;
            echo "<strong>Tipo de valor: </strong>Selecione uma opção.<br>";
        }
        if (empty($itens)) {
            $erro = true;
            echo "<strong>Item: </strong>Selecione uma opção.<br>";
        }
        if (empty($type_form)) {
            $erro = true;
            echo "<strong>Tipo do campo do formulário: </strong>Selecione uma opção.<br>";
        }
        if (empty($ordem_form)) {
            $erro = true;
            echo "<strong>Ordem do campo no formulário: </strong>Selecione uma opção.<br>";
        }
        else {
            if (!preg_match("/^[1-9][0-9]*$/", $ordem_form)) {
                $erro = true;
                echo "<strong>Ordem do campo no formulário: </strong>Introduza um numero superior a 0.<br>";
            }
        }
        if (empty($rname)) {
            $erro = true;
            echo "<strong>Obrigatorio: </strong>Selecione uma opção.<br>";
        }
        if($rname == "yes") {
            $obrigatorio = 1;
        }
        elseif ($rname == "no") {
            $obrigatorio = 0;
        }

    }
    if (isset($_POST['inserir']) && $erro == false) {
        $string = "SELECT DISTINCT SUBSTRING('$itens', 1, 3) AS item_id FROM subitem";
        $results = mysqli_query($link,$string);
        while ($row = mysqli_fetch_assoc($results)){
            $tresletras = $row['item_id'];
        }
        $verify_item_id = "SELECT item.name,item.id FROM item";
        $resulty_i = mysqli_query($link,$verify_item_id);
        while ($row = mysqli_fetch_assoc($resulty_i)){
            if ($row['name'] == $itens){
                $i = $row['id'];
            }
        }
        $verify_unit_id = "SELECT subitem_unit_type.name,subitem_unit_type.id FROM subitem_unit_type";
        $resulty_u = mysqli_query($link,$verify_unit_id);
        while ($row = mysqli_fetch_assoc($resulty_u)){
            if ($row['name'] == $subitem_unit_type){
                $unit = $row['id'];
            }
            elseif($subitem_unit_type == "null"){
                $unit = "null";
            }
        }

        $string2 = "INSERT INTO subitem (name, item_id,value_type, form_field_name,form_field_type,unit_type_id,form_field_order, mandatory,state) VALUES ('$nome','$i','$value_type','','$type_form','$unit','$ordem_form','$obrigatorio','active')";
        $string3 = "INSERT INTO subitem (name, item_id,value_type, form_field_name,form_field_type,unit_type_id,form_field_order, mandatory,state) VALUES ('$nome','$i','$value_type','','$type_form',NULL,'$ordem_form','$obrigatorio','active')";
        if ($unit == "null") {
            $results3 = mysqli_query($link,$string3);
        }
        else {
            $results2 = mysqli_query($link,$string2);
        }
        $lastID = mysqli_insert_id($link);
        $field_name = $tresletras."-".$lastID."-".$nome;
        $update = "UPDATE subitem SET form_field_name='$field_name' WHERE subitem.id=$lastID";
        $rupdate = mysqli_query($link,$update);
        echo "<ul> Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão correctos e pretende submeter os mesmos? <br>  
                        <li> Nome do subitem: '$nome'</li><br>
                        <li> Item: '$itens'</li><br>
                        <li> Tipo de valor: '$value_type'</li><br>
                        <li> Nome do formulário: '$field_name'</li><br>
                        <li> Tipo do campo do formulário: '$type_form'</li><br>
                        <li> Tipo de unidade:  '$subitem_unit_type'</li><br>
                        <li> Ordem do campo no formulário: '$ordem_form'</li><br>
                        <li> Obrigatório: '$obrigatorio'</li><br>
                   </ul>
                <form action='' method='post'> 
                 <input type='hidden' name='continuar' value='continuar'/>
                 <input type='submit' name='submit' value='submit'><br>
                 <input type='hidden' name=nome value=' .$nome. '/>
                 <input type='hidden' name=itens value=' .$itens. '/>
                 <input type='hidden' name=value_type value=' .$value_type. '/>
                 <input type='hidden' name='ordem_form' value=' .$ordem_form . '/>
                 <input type='hidden' name='subitem_unit_type' value=' .$subitem_unit_type . '/>
                 <input type='hidden' name='rname' value=' .$obrigatorio. '/>
              </form>";
    }
    if (isset($_POST['continuar'])) {

        echo "<br><strong>Inseriu os dados de novo subitem com sucesso.</strong>";
        echo '<form action="" method="post">
               <input type=submit name=Submit value="continuar">
              </form>';
    }
}

echo "<script type='text/javascript'>document.write(\"<a href='javascript:history.back()' class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>\");</script>
<noscript>
<a href='".$_SERVER['HTTP_REFERER']."‘ class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>
</noscript>";
?>