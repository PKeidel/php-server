<?php

namespace PKeidel\Server\UDP;

use PKeidel\Server\DNS\Packet\ArrayReader;

abstract class UDPPacket {
    public ArrayReader $arrayReader;

    public function __construct(string $buffer) {
        $this->arrayReader = new ArrayReader(unpack('C*', $buffer));
        $this->parseData();
    }

}
