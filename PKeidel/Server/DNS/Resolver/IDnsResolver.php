<?php

namespace PKeidel\Server\DNS\Resolver;

use PKeidel\Server\DNS\Packet\DNSPacket;

interface IDnsResolver {
    /**
     * @param DNSPacket $request
     * @return Answer[]
     */
    public function getAnswersFor(DNSPacket $request): array;
}
