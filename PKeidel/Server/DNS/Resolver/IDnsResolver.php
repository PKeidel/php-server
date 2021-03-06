<?php

namespace PKeidel\Server\DNS\Resolver;

use PKeidel\Server\DNS\Packet\DNSPacket;

interface IDnsResolver {
    /**
     * @param DNSPacket $request
     * @return Resource[]
     */
    public function getAnswersFor(DNSPacket $request): array;
}
