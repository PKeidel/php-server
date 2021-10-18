<?php

namespace PKeidel\Server\DNS\Server;

use PKeidel\Server\DNS\Packet\Question;
use PKeidel\Server\DNS\Resolver\IDnsResolver;
use PKeidel\Server\UDP\Server as UDPServer;
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
        echo "\nhandlePacket($ip, $port, ".bin2hex($buffer).")\n";

        $request = new DNSPacket($buffer);

        if(!$request->qdCount) {
            return;
        }

        $q = implode(", ", array_map(function(Question $q) {
            return $q->getTypeStr() . ' ' . $q->domain;
        }, $request->qd));
        echo "├── incomming question: $ip asked for: $q\n";

        $response = Response::fromRequest($request);

        // set Recurstion available = 0
        $response->flags->setRA(0);

        $answers = $this->resolver->getAnswersFor($request);
        echo "├── answers: ".implode(', ', $answers)."\n";

        $response->setAnswers($answers);

        echo "└── sending: ".bin2hex($response->toRaw())."\n";

        if(!defined('IS_DEBUG')) {
            $raw = $response->toRaw();
            socket_sendto($this->socket, $raw, strlen($raw), 0, $ip, $port);
        }
    }
}
