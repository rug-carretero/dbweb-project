<?php
function generateSalt(){
    $salt = uniqid(mt_rand(), true);
    return $salt;
}
?>