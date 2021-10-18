<?php

namespace PKeidel\Server\DNS\Server;

use PKeidel\Server\DNS\Resolver\IDnsResolver;
use PKeidel\Server\UDP\Server as UDPServer;
use PKeidel\Server\DNS\Packet\Data;
use PKeidel\Server\DNS\Packet\DNSPacket;
use PKeidel\Server\DNS\Packet\Response;

// https://github.com/awaysoft/AwayDNS/blob/master/libs/Server.php

class Server extends UDPServer {
    /** @var IDnsResolver|null  */
    private   $resolver    = NULL;

    public function __construct(IDnsResolver $resolver) {
        $this->resolver = $resolver;
    }

    public function handlePacket($ip, $port, $buffer) {
        echo "handlePacket($ip, $port, ".bin2hex($buffer).")\n";

        $request = new DNSPacket($buffer);

        if(!$request->qdCount) {
            return;
        }

        $q = implode(", ", array_map(function(Data $q) {
            return $q->domain;
        }, $request->qd));
        echo "Incomming question: $ip asked for: $q\n";

        $response = Response::fromRequest($request);

        // set Recurstion available = 0
        $response->flags->setRA(0);

        $answers = $this->resolver->getAnswersFor($request);
        echo "answers:   ".implode(', ', $answers)."\n";

        $response->setAnswers($answers);

        echo "sending: ".implode(' ', explode("\n", chunk_split(bin2hex($response->toRaw()), 2, "\n")))."\n";

        if(!defined('IS_DEBUG')) {
            $raw = $response->toRaw();
            $res = socket_sendto($this->socket, $raw, strlen($raw), 0, $ip, $port);
            echo "socket_sendto => $res\n";
        }
    }
}
