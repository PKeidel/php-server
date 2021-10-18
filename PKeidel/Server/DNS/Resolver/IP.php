<?php

namespace PKeidel\Server\DNS\Resolver;

class IP {

    public static function readableToRaw(int $b1, int $b2, int $b3, int $b4) {
        return pack('C*', $b1, $b2, $b3, $b4);
    }
    public static function readableArrToRaw(array $parts) {
        return pack('C*', intval($parts[0]), intval($parts[1]), intval($parts[2]), intval($parts[3]));
    }
}
