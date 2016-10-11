<?php

    function print_error($db,$message){
        echo "<h1>CHYBA !!!</h1>";
        echo "<p class='message'>$message</p>";
        echo "<p class='error'>".$db->error."</p>";
    }
?>