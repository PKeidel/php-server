<?php

namespace PKeidel\Server\DNS\Resolver;

class IP {

    public static function readableToRaw(int $b1, int $b2, int $b3, int $b4) {
        return pack('C*', $b1, $b2, $b3, $b4);
    }
    public static function readableArrToRaw(array $parts) {
        return pack('C*', intval($parts[0]), intval($parts[1]), intval($parts[2]), intval($parts[3]));
    }

    public static function intToReadable(int $data): string {
        $b1 = $data >> 24;
        $b2 = ($data >> 16) & 0xFF;
        $b3 = ($data >> 8) & 0xFF;
        $b4 = $data & 0xFF;
        return "$b1.$b2.$b3.$b4";
    }

    public static function bin2string(string $binString): string {
        return implode('.', unpack('C*', $binString));
    }
}
