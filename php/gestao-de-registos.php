<?php
require_once("custom/php/common.php");

$link = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if (!$link) {
    echo "Connection failed!";
    die("Connection failed: " . mysqli_connect_error());
}
else {
    //echo 'Connected successfully';
}

if ( !is_user_logged_in() ) {
    die("Não tem autorização para aceder a esta página");
}
if (!current_user_can('manage_records')){
    die("Não tem autorização para aceder a esta página");
}

$nome_completo = trim($_POST["nome_completo"]);
$data_de_nascimento = trim($_POST["data_de_nascimento"]);
$nome_encarregado = trim($_POST["nome_encarregado"]);
$telefone = trim($_POST["telefone"]);
$email = trim($_POST["email"]);

if (( !isset($_POST['inserir']) && !isset($_POST['validar']) )) {
    $query = "SELECT * FROM child";
    $result = mysqli_query($link,$query);
    if(sizeof($result) == 0){
        echo "<strong> Não há crianças.</strong>";
    }
    else {
        echo "<table>
            <tr>
            <th>Nome</th>
            <th>Data de nascimento</th>
            <th>Enc. de educação</th>
            <th>Telefone do Enc.</th>
            <th>e-mail</th>
            <th>registos</th>
        </tr>";
    }

    $query1 = "SELECT * FROM child ORDER BY child.name ASC";
    $result1 = mysqli_query($link,$query1);
    while($row = mysqli_fetch_assoc($result1)) {
        echo "<tr><td>".$row["name"]."</td><td>".$row["birth_date"]."</td><td>".$row["tutor_name"]."</td><td>".$row["tutor_phone"]."</td><td>".$row["tutor_email"]."</td><td>";
        $x = $row['id'];
        $query2 = "SELECT item.name,item.id FROM item WHERE item.id<=3 ORDER BY item.name ASC";
        $result2 = mysqli_query($link,$query2);
        while ($row2 = mysqli_fetch_assoc($result2)) {
            echo "<br>".$row2['name'] . ": ";
            $z = $row2['id'];
            $query3 = "SELECT child.id,subitem.item_id,value.value,subitem.name FROM child,value,subitem,item WHERE value.child_id=child.id AND item.id=subitem.item_id AND value.subitem_id=subitem.id ORDER BY value.id ASC ";
            $result3 = mysqli_query($link,$query3);
            while ($row3 = mysqli_fetch_assoc($result3)){
                if ($x == $row3['id']){
                    if ($z == $row3['item_id']){
                        echo "<strong>" . $row3["name"] . "</strong>";
                        echo " (" . $row3["value"] . "); ";
                    }
                }
            }
        }
        echo "</tr>";
    }
    echo"</table>";

    echo '<h3>Dados de registo - introdução</h3>
          <p> Introduza os dados pessoais básicos da criança: </p>
         
          <form action="" method="post" id="registo">
            <label for=nome_completo>Nome Completo:</label>
            <input type=text id=nome_completo name=nome_completo placeholder="Obrigatório" /> <br>
        
            <label for="data_de_nascimento">Data de nascimento (AAAA-MM-DD):</label>
		    <input type="text" name="data_de_nascimento" id="data_de_nascimento" /> <br>
			
		    <label for=nome_encarregado>Nome do Encarregado de Educação:</label>
            <input type=text name=nome_encarregado id=nome_encarregado placeholder="Obrigatório" /> <br>
		
		    <label for=telefone>Telefone do Encarregado de Educação:</label>	
		    <input type="text" name="telefone" id="telefone" maxlength="9" placeholder="Obrigatório" /> <br>
			
		    <label for=email>Email:</label>
            <input type=text name=email id=email placeholder="Optional"/> <br>
			
		    <input type=hidden name="validar" value="validar"/>	
		    <input type="submit" name="submit" value="submit"/>
          </form>
          <script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
          <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.js"></script>
          <script scr="gestao-de-registos.js"></script>
          <script>$("#registo")</script>';
}
else {
    if(isset($_POST["validar"])) {

        $nome_error = false;
        $datadenascimento_error = false;
        $nomeencarregado_error = false;
        $telefone_error = false;
        $email_error = false;

        echo "<h3>Dados de registo - validação</h3>";

        if(empty($nome_completo)) {
            $nome_error = true;
            echo "<strong>Nome: </strong>Introduza um nome válido.<br>";
        }
        else {
            if(!preg_match("~^\p{L}+(?:[-\h']\p{L}+)*$~u", $nome_completo)) {
                $nome_error = true;
                echo "<strong>Nome: </strong>Introduza apenas letras e espaços.<br>";
            }
        }
        if(empty($data_de_nascimento)) {
            $datadenascimento_error = true;
            echo "<strong>Data de nascimento: </strong>Introduza uma data válida.<br>";
        }
        else {
            if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$data_de_nascimento)) {
                $datadenascimento_error = true;
                echo "<strong>Data de nascimento: </strong>Formato errado.<br>";
            }
        }
        if(empty($nome_encarregado)) {
            $nomeencarregado_error = true;
            echo "<strong>Nome Encarregado de End.: </strong>Introduza um nome válido.<br>";
        }
        else {
            if(!preg_match("~^\p{L}+(?:[-\h']\p{L}+)*$~u", $nome_encarregado)) {
                $nomeencarregado_error = true;
                echo "<strong>Nome Encarregado de End.: </strong>Introduza apenas Letras e espaços.<br>";
            }
        }
        if(empty($telefone)) {
            $telefone_error = true;
            echo "<strong>Telefone: </strong> Introduza um nº de telefone válido.<br>";
        }
        else {
            if(!preg_match("/^[9][0-9]{8}$/", $telefone)) {
                $telefone_error = true;
                echo "<strong>Telefone: </strong>Introduza apenas números.<br>";
            }
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_error = true;
            echo "<strong>Email: </strong>Endereço de email inválido.<br>";
        }
        elseif (!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/", $email)){
            $email_error = true;
            echo "<strong>Email: </strong>Endereço de email inválido.<br>";
        }
    }
    if(isset($_POST['validar']) && $nome_error == false && $datadenascimento_error == false && $nomeencarregado_error == false && $telefone_error == false && $email_error == false) {
            echo '<ul> Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão correctos e pretende submeter os mesmos? <br>  
                        <li> Nome completo da criança: ' . $nome_completo . '</li><br>
                        <li> Data de nascimento da criança: ' . $data_de_nascimento . '</li><br>
                        <li> Nome do Encarregado de Educação: ' . $nome_encarregado . '</li><br>
                        <li> Telefone:  ' . $telefone . '</li><br>
                        <li> Email do tutor: ' .$email. '</li><br>
                   </ul>
                   <form action="" method="post"> 
                        <input type="hidden" name="inserir" value="inserir"/>
                        <input type="submit" name="Submit" value="CONTINUAR"/><br>
                        <input type=hidden id=nome_completo name=nome_completo value="' .$nome_completo. '"/>
                        <input type=hidden id=data_de_nascimento name=data_de_nascimento value="' .$data_de_nascimento. '"/>
                        <input type=hidden id=nome_encarregado name=nome_encarregado value="' .$nome_encarregado. '"/>
                        <input type=hidden id=telefone name=telefone value="' .$telefone. '"/>
                        <input type="hidden" id="email" name="email" value="' .$email . '"/>
                   </form>';
    }
    if (isset($_POST['inserir'])) {
        $query4 = "INSERT INTO child (name, birth_date, tutor_name, tutor_phone, tutor_email) VALUES ('$nome_completo','$data_de_nascimento','$nome_encarregado','$telefone','$email')";
        $result4 = mysqli_query($link, $query4);
        echo "<br><strong>A introdução dos dados foi feita com sucesso!</strong>";
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