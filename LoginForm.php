<?php

    $definedVars = get_defined_vars();
    !array_key_exists('message', $definedVars) && $message = '';
    
?>
<html>
    <head>
        <title>Netatmo Weather Station Authorization</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div>
            <?php if(!empty($message)): ?>
                <h2><?= $message ?></h2>
            <?php endif; ?>
            <form action="<?= $url ?>" method="POST">
                <input name="state" type="hidden" value="<?= $state ?>">
                <div>LOGIN: <input name="login" type="email" value="<?= $userName ?>" /></div>
                <div>PASSWORD: <input name="password" type="password" /></div>
                <div><input type="submit" value="SUBMIT"></div>
            </form>
        </div>
    </body>
</html>