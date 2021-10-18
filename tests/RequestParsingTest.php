<?php

use PHPUnit\Framework\TestCase;
use PKeidel\Server\DNS\Packet\DNSPacket;

class RequestParsingTest extends TestCase {

    public function test01() {
        $hexStr = 'd65e01000001000000000000056d74616c6b06676f6f676c6503636f6d0000010001';

        $bytes = hex2bin($hexStr);
        $response = new DNSPacket($bytes);

        self::assertEquals('d65e', dechex($response->txid), 'Wrong txid');

        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $response->flags,
            'Wrong headerflags string'
        );

        self::assertEquals(1, $response->qdCount, 'Wrong qdCount');
        self::assertEquals(0, $response->anCount, 'Wrong anCount');
        self::assertEquals(0, $response->nsCount, 'Wrong nsCount');
        self::assertEquals(0, $response->arCount, 'Wrong arCount');

        self::assertEquals($hexStr, bin2hex($response->toRaw()), 'Generated hex string is different from generated one');

    }

}
