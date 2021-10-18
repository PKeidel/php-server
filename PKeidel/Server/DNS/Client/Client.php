<?php

namespace PKeidel\Server\DNS\Client;

use PKeidel\Server\DNS\Packet\DNSPacket;
use PKeidel\Server\DNS\Resolver\Answer;
use PKeidel\Server\DNS\Resolver\IP;

class Client {
//    /**
//     * @param $type
//     * @param $class
//     * @param $domain
//     * @return Answer[]
//     */
//    public function askFor($type, $class, $domain): array {
//        // get external IP
//        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
//        socket_connect($sock, "8.8.8.8", 53);
//        socket_getsockname($sock, $ip);
//        socket_close($sock);
//
//        echo "    IP: $ip\n";
//
//        $ipParts = explode('.', $ip);
//        $ipParts[3] = 1;
//        $dnsServerIp = implode('.', $ipParts);
//
//        $dnsServerIp = '192.168.0.1';
//
//        echo "    3rd party DNS Server IP: $dnsServerIp\n";
//
//        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
//
//        // ask for "A open.spotify.com"
//        $buf    = hex2bin("b50301000001000000000000046f70656e0773706f7469667903636f6d0000010001");
//        $len    = strlen($buf);
//        $flags  = 0;
//        $port   = 53;
//
//        // send question
//        socket_sendto($socket, $buf, $len, $flags, $dnsServerIp, $port);
//
//        // Get answer
//        $len    = 512;
//        echo "waiting for socket_recvfrom ...\n";
//        socket_recvfrom($socket, $buf, $len, $flags, $dnsServerIp, $port);
//        echo "  got an answer! => ".bin2hex($buf)."\n";
//
//        socket_close($socket);
//        echo "6\n";
//
//        $request  = Request::parse($buf);
//        print_r($request);
//
//        $answer = new Answer();
//        $answer->name     = "$dnsrecord->hostprefix.$dnsrecord->host";
//        $answer->type     = ['A' => 1][$dnsrecord->type] ?? 0xFF;
//        $answer->class    = ['IN' => 1, 'CS' => 2, 'CH' => 3, 'HS' => 4][$dnsrecord->class] ?? 0xFF;
//        $answer->ttl      = $dnsrecord->ttl;
//        $answer->dataIsIp = true;
//        $answer->data     = IP::readableArrToRaw(explode('.', $dnsrecord->ip));
//
//        return [$answer];
//    }

    public function askForRaw($dnsServerIp, $raw) {
        $newId = random_bytes(2);
        $raw[0] = $newId[0];
        $raw[1] = $newId[1];

        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

        $len    = strlen($raw);
        $flags  = 0;
        $port   = 53;

        socket_sendto($socket, $raw, $len, $flags, $dnsServerIp, $port);

        // Get answer
        $len    = 512;
        echo "waiting for socket_recvfrom ...\n";
        socket_recvfrom($socket, $buf, $len, $flags, $dnsServerIp, $port);
        echo "  got an answer! => ".bin2hex($buf)."\n";
        socket_close($socket);

        $response = new DNSPacket($buf);
        dump($response);

        $answer = new Answer();
        $answer->name     = $response->an[0]->domain;
        $answer->type     = ['A' => 1][$dnsrecord->type] ?? 0xFF;
        $answer->class    = ['IN' => 1, 'CS' => 2, 'CH' => 3, 'HS' => 4][$dnsrecord->class] ?? 0xFF;
        $answer->ttl      = $dnsrecord->ttl;
        $answer->dataIsIp = true;
        $answer->data     = IP::readableArrToRaw(explode('.', $dnsrecord->ip));

        return [$answer];
    }
}
