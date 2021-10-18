<?php

namespace PKeidel\Server\DNS\Client;

use PKeidel\Server\DNS\Packet\Answer;
use PKeidel\Server\DNS\Packet\DNSPacket;

class Client
{

    /**
     * @param $dnsServerIp
     * @param $raw
     * @return Answer[]
     * @throws \Exception
     */
    public function askForRaw($dnsServerIp, $raw): array
    {

        echo "│   ├── askForRaw RAW: " . bin2hex($raw) . "\n";

        $newId = random_bytes(2);
        $raw[0] = $newId[0];
        $raw[1] = $newId[1];

        // set Additional RR Cout to 0
        $raw[11] = chr(0);

        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_set_nonblock($socket);
        socket_clear_error($socket);

        $len = strlen($raw);
        $flags = 0;
        $port = (int)(env('PORT') ?? '53');

        echo "│   ├── socket_sendto(socket, raw, $len, $flags, $dnsServerIp, $port)\n";
        socket_sendto($socket, $raw, $len, $flags, $dnsServerIp, $port);

        // Get answer
        $len = 512;
        $timeoutSec = 0.4;
        $sleepMicroSec = 10_000;
        echo "│   ├── waiting for socket_recvfrom ...\n";
        $startTime = microtime(true);
        do {
            usleep($sleepMicroSec += 10_000);
            $read = socket_recvfrom($socket, $buf, $len, $flags, $dnsServerIp, $port);
            echo "│   │   │  └── got: " . var_export($read, true) . "\n";
            if ($read === FALSE) {

                // check for timeout
                $currentTime = microtime(true);
                if (($currentTime - $startTime) > $timeoutSec) {
                    echo "│   │   └── got an TIMEOUT after $timeoutSec seconds\n";
                    socket_close($socket);
                    return [];
                }

                $errorCode = socket_last_error($socket);
                socket_clear_error($socket);
                if ($errorCode !== 11) {
                    echo "│   │   └── got an ERROR! => " . socket_strerror($errorCode) . " ($errorCode)\n";
                    socket_close($socket);
                    return [];
                }
            }
        } while ($read === FALSE);

        echo "│   │   └── got an answer! => " . bin2hex($buf) . "\n";
        socket_close($socket);

        $response = new DNSPacket($buf);

        echo "│   └── Package contains " . $response->anCount . " answers\n";

        return $response->an;
    }
}
