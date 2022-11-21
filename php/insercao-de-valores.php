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
if (!current_user_can('insert_values')){
    die("Não tem autorização para aceder a esta página");
}
$nome = trim($_POST["nome"]);
$data_nascimento=trim($_POST["data_nascimento"]);
$child_id=trim($_REQUEST["id"]);

if (!isset($_REQUEST['escolher_crianca']) &&  !(isset($_GET['estado']) == "escolher_item&crianca") && !(isset($_GET['estado'])=="introducao&item")){
    echo"<h3>Inserção de valores - criança - procurar</h3>
        <form action='' method='POST'>
        <p>Nome: <input type='text' name='nome' ></p>
        <p>Data de nascimento: <input type='text' name='data_nascimento' id='data_nascimento' placeholder='AAAA-MM-DD'></p>
        <input type=hidden name='escolher_crianca' value='escolher_crianca'/>
        <p><input type='submit' name='submit' value='Submit'/></p>    
        </form>";
}
else {
    if (isset($_REQUEST['escolher_crianca']) ) {
        echo "<h3>Inserção de valores - criança - escolher</h3>";

        if($nome != '' and $data_nascimento ==''){
            $query1 = "SELECT child.id, child.name, child.birth_date FROM child WHERE child.name LIKE '$nome'";
            $result1 = mysqli_query($link, $query1);
            while($row = mysqli_fetch_assoc($result1)){
                $child_id = $row["id"];
                echo "<form method='get' action=insercao-de-valores?estado=escolher_item&crianca='.$child_id.'>";
                echo '<a href="insercao-de-valores?estado=escolher_item&crianca='.$child_id.'">'.'['.$row['name'].'] </a> ('.$row['birth_date'].')';
                echo "<input type='hidden' name='child_id' value='.$child_id.'>
                        </form>";
            }
        }
        elseif ($data_nascimento!='' and $nome ==''){
            $query2 = "SELECT child.id,child.name, child.birth_date FROM child WHERE child.birth_date LIKE '$data_nascimento'";
            $result2 = mysqli_query($link, $query2);
            while($row = mysqli_fetch_assoc($result2)){
                $child_id = $row["id"];
                echo "<form method='get' action=insercao-de-valores?estado=escolher_item&crianca='.$child_id.'>";
                echo '<a href="insercao-de-valores?estado=escolher_item&crianca='.$child_id.'">'.'['.$row['name'].'] </a> ('.$row['birth_date'].')';
                echo "<input type='hidden' name='child_id' value='.$child_id.'>
                        </form>";

            }
        }
        elseif($data_nascimento!= '' and $nome!= ''){
            $query3 = "SELECT child.id,child.name, child.birth_date FROM child WHERE child.birth_date LIKE '$data_nascimento' AND WHERE child.name LIKE '$nome'";
            $result3 = mysqli_query($link, $query3);
            while($row = mysqli_fetch_assoc($result3)){
                $child_id = $row["id"];
                echo "<form method='get' action=insercao-de-valores?estado=escolher_item&crianca='.$child_id.'>";
                echo '<a href="insercao-de-valores?estado=escolher_item&crianca='.$child_id.'">'.'['.$row['name'].'] </a> ('.$row['birth_date'].')';
                echo "<input type='hidden' name='child_id' value='.$child_id.'>
                        </form>";

            }
        }
        else{
            $query4 = "SELECT child.id,child.name, child.birth_date FROM child ";
            $result4 = mysqli_query($link, $query4);
            while($row = mysqli_fetch_assoc($result4)){
                $child_id = $row["id"];
                echo "<form method='get' action=insercao-de-valores?estado=escolher_item&crianca='.$child_id.'>";
                echo '<a href="insercao-de-valores?estado=escolher_item&crianca='.$child_id.'">'.'['.$row['name'].'] </a> ('.$row['birth_date'].')';
                echo "<input type='hidden' name='child_id' value='.$child_id.'>
                        </form>";

            }
        }
    }
    if(isset($_GET['estado']) == "escolher_item&crianca" ){
        echo"<h3>Inserção de valores - escolher item</h3>";
        $query5="SELECT item_type.name,item_type.id FROM item_type";
        $result5 = mysqli_query($link, $query5);
        while($row=mysqli_fetch_assoc($result5)){
            echo '<ul><li>'.$row["name"].'</li>';
            $m=$row["id"];
            $query6 = "SELECT DISTINCT item.id,item.name , item.item_type_id FROM item , subitem WHERE item.id=subitem.item_id";
            $result6 = mysqli_query($link, $query6);
            while($row = mysqli_fetch_assoc($result6)){
                if($m==$row["item_type_id"]){
                    $i=$row["id"];
                    $in=$row["name"];
                    $it=$row["item_type_id"];
                    echo "<form method='get' action=insercao-de-valores?estado=introducao&item='.$i.'>";
                    echo '<a href="insercao-de-valores?estado=introducao&item='.$i.'">   <li>['.$row["name"].']</li></a>';
                    echo "<input type='hidden' name='item_id' value='.$i.'><input type='hidden' name='item_name' value='.$in.'><input type='hidden' name='child_id' value='.$child_id.'><input type='hidden' name='item_type_id' value='.$it.'>
                          </form>";
                }
            }
            echo '</ul>';
        }
    }
    if(isset($_GET['estado']) == "introducao&item"){
        echo"<h3>Inserção de valores - $in</h3>";
        $query7="SELECT DISTINCT subitem.item_id , subitem.name FROM subitem, item WHERE subitem.item_id LIKE '$i' ORDER BY subitem.form_field_order ASC";
        $result7=mysqli_query($link,$query7);
        while($row = mysqli_fetch_assoc($result7)){
            echo '<li>'.$row["name"].'</li>';
        }
    }
}


echo "<br><script type='text/javascript'>document.write(\"<a href='javascript:history.back()' class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>\");</script>
<noscript>
<a href='".$_SERVER['HTTP_REFERER']."‘ class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>
</noscript>";
?>