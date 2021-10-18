<?php

namespace PKeidel\Server\UDP;

class Server {
    protected $socket;

    /**
     * @param $serverIP
     * @param $serverPort
     * @throws \Exception
     */
    public function start($serverIP, $serverPort) {
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

//        echo "  socket_bind(.., $serverIP, $serverPort)\n";
        socket_bind($this->socket, $serverIP, $serverPort) or die("Failed to bind to $serverIP:$serverPort");

        while (true) {
            $response = socket_recvfrom($this->socket, $buffer, 32768, 0, $ip, $port);
            if($response === FALSE) {
                throw new \Exception("Failed to receive packet");
            }
            echo "\$response = $response\n";
            $this->handlePacket($ip, $port, $buffer);
        }
    }

    public function handlePacket($ip, $port, $buffer) {
        echo "not implemented yet: ".get_class($this)."::handlePacket($ip, $port, ".base64_encode($buffer).")\n";
    }

    public function stop() {
        echo get_class($this)."::stop()\n";
        socket_close($this->socket);
    }
}
