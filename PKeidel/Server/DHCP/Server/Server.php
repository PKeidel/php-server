<?php

namespace PKeidel\Server\DHCP\Server;

use PKeidel\Server\DHCP\Request;
use PKeidel\Server\UDP\Server as UDPServer;

class Server extends UDPServer {

    public function handlePacket($ip, $port, $buffer) {
        echo "handlePacket($ip, $port, ".bin2hex($buffer).")\n";

        $request  = Request::parse($buffer);
    }
}
