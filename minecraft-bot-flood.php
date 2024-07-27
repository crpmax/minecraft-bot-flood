<?php

/**
 * Simple PHP Minecraft bot flood
 * 
 * This script is used for stress testing Minecraft servers,
 * it can connect a lot of bots very fast.
 * These bots will not respond to any server packets,
 * so they will time out after 30 seconds by default.
 * 
 * @author CreeperMaxCZ <creepermaxcz@gmail.com>
 * 
 * @version 1.0.1
 */


//Check script arguments
if ($argc != 5) {
    echo "Usage: php $argv[0] <protoversion> <ip> <port> <count>" . PHP_EOL;
    echo "Get protoversion from https://wiki.vg/Protocol_version_numbers" . PHP_EOL;
    exit();
}

//Minecraft protocol version (47 = 1.8.x, 340 = 1.12.2, 498 = 1.14.4, 754 = 1.16.4 ...)
$proto = intval($argv[1]);

$ip = $argv[2];
$port = intval($argv[3]);
$count = intval($argv[4]);

$sockets = [];

//Make handshake packet
$data = "\x00";
$data .= makeVarInt($proto);
$data .= pack('c', strlen($ip)) . $ip;
$data .= pack('n', $port);
$data .= "\x02";
$handshake = pack('c', strlen($data)) . $data;

for ($i = 1; $i <= $count; $i++) {

    //Generate random nickname
    //Do not use openssl, because people have problems installing this extension :(
    //$nick = bin2hex(openssl_random_pseudo_bytes(5));
    $nick = generateRandomString(16);

    //Create TCP socket
    $socket = @stream_socket_client("tcp://$ip:$port", $errno, $errstr, 10);

    //Check for errors
    if ($errno > 0) {
        echo "ERROR: " . $errstr . PHP_EOL;
        continue;
    }

    $sockets[] = $socket;

    echo "\r[$i/$count] Connecting bot: $nick";

    //Send login handshake packet
    fwrite($socket, $handshake);

    //Make login start packet
    $data = "\x00";
    $data .= pack('c', strlen($nick)) . $nick;

    // from 1.19 to < 1.19.3
    if ($proto >= 759 && $proto < 761) {
        // boolean disable encryption and do not send uuid
        $data .= "\x00\x00";
    }

    // from 1.19.3 to < 1.20.2
    if ($proto >= 761 && $proto < 764) {
        // boolean do not send uuid
        $data .= "\x00";
    }

    // from 1.20.2
    if ($proto >= 764) {
        // send empty uuid
        $data .= generateRandomString(16);
    }

    $data = pack('c', strlen($data)) . $data;

    //Send login start packet
    fwrite($socket,  $data);

    if ($proto >= 764) {
        usleep(100000);
        // login ack packet
        $data = "\x02\x00\x03";
        fwrite($socket, $data);

        // client data finish packet
        $data = "\x02\x00\x02";
        fwrite($socket, $data);
    }
}

echo "\rAll $count bots connected, waiting till they drop" . PHP_EOL;

//Wait 33 seconds till all bots are timed out
sleep(33);

function makeVarInt($data)
{
    if ($data < 0x80) {
        return pack('C', $data);
    }

    $bytes = [];
    while ($data > 0) {
        $bytes[] = 0x80 | ($data & 0x7f);
        $data >>= 7;
    }

    $bytes[count($bytes) - 1] &= 0x7f;

    return call_user_func_array('pack', array_merge(array('C*'), $bytes));
}

//From: https://stackoverflow.com/questions/4356289/php-random-string-generator
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
