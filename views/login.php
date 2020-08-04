<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link href="<?=BASE_URL?>css/main.css" rel="stylesheet" type="text/css">
</head>
<body>

<form method="post">

    <h3>Авторизация</h3><br>

    <div>
        <div>Подразделение:</div>
        <div>
            <select name="id_organisation" class="input input_text" required>
                <option value="">Выберите подразделение</option>
                <?php
                    foreach ($organisations as $org) {
                        echo '<option value="' . $org['id_organisation'] . '">' . $org['name'];
                        echo '</option>';
                    }
                ?>
            </select>
        </div>
    </div>

    <div>
        <div>Логин:</div>
        <div>
            <input type="text" name="login" class="input input_text" required>
        </div>
    </div>

    <div>
        <div>Пароль:</div>
        <div>
            <input type="password" name="pass" class="input input_text" required> 
        </div>
    </div>

    <div class="wdth">
        <h3 class="msg"><?=$msg?></h3>
    </div>
    
    <div>
        <input type="submit" name="submit" value="Вход" class="input input_button">
    </div>

</form>

</body>
</html>
