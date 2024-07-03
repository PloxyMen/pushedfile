<?php

function readDomaninsFromFile($filename)
{
    $domains = [];

    if(file_exists($filename)) {
        $file = fopen($filename, "r");

        while(($line = fgets($file)) !== false){
            $parts = explode(' , ',trim($line));

            if(count($parts) == 2) {
                $domain = trim($parts[0]);
                $expireDate = trim($parts[1]);


                $expireDateFormatted = date('Y-m-d', strtotime($expireDate));

                $domains[] = [
                    'domain' => $domain,
                    'expireDate' => $expireDateFormatted,
                    'expireDateFormatted' => $expireDateFormatted
                ];
            }
        }

        fclose($file);
    }else {
        echo "Файл не найден!";
        http_response_code(500);
    }
    return $domains;
}


function checkDomainExpirations($domains){
    $notifications = [];
    $currentDate = date('Y-m-d');
    $alertThreshold = 30;

    foreach($domains as $domainInfo) {
        $expireDate = $domainInfo['expireDateFormatted'];
        $daysLeft = (strtotime($expireDate) - strtotime($currentDate)) / (60 * 60 * 24);

        if($daysLeft >= $alertThreshold) {
            $notifications[] = "Домен" . $domainInfo['domain'] . " (https://" . $domainInfo['domain'] ."/ ) действует до " . $domainInfo['expireDate'] .
                ", нужно провести продление действия домена!";
        }
    }
    return $notifications;
}

// Путь к файлу
$filename = 'name.txt';

// Чтение данных из файла
$domains = readDomaninsFromFile($filename);

// Проверка даты истечения и формирования уведомлений
$notifications = checkDomainExpirations($domains);

//Вывод уведомлений

foreach($notifications as $notification) {
    echo $notification . "/n";
}

http_response_code(200);