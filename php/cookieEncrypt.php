<?php
function cookieEncrypt($plainText)//base64 cookie加密
{
    return base64_encode("dcb82435" . $plainText . "4118c3d2");
}

function cookieDecrypt($ciphertext)//解密
{
    $tmp = base64_decode($ciphertext);
    return substr($tmp, 8, strlen($tmp) - 16);
}
