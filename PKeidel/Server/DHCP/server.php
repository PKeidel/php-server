<?php

namespace PKeidel\Server\DHCP;

use PKeidel\Server\DHCP\Server\Server as DHCPServer;

spl_autoload_register(function($name) {
    require_once(str_replace('\\', '/', $name).'.php');
});

// https://tools.ietf.org/html/rfc2131

$port = 67;

$ip = '0.0.0.0';

//$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
//socket_connect($sock, "8.8.8.8", $port);
//socket_getsockname($sock, $ip);

echo "listening on ip $ip:$port\n";

$dhcpServer = new DHCPServer();

//if($argc > 1 && $argv[1] === '--debug') {
//    define('IS_DEBUG', true);
//
//    $buffer = hex2bin("9e4f0120000100000000000106676f6f676c6503636f6d00000100010000291000000000000000");
//    $server->handlePacket($ip, 54321, $buffer);
////    echo "\n\nErwartete Antwort:\n";
////    echo "         ".implode(' ', explode("\n", chunk_split("", 2, "\n")))."\n";
//    exit;
//}

$dhcpServer->start($ip, $port);
