<?php

class HeaderFlagsTest extends TestCase {

    public function testHeaderFlagsParsing01() {
        $headerFlags = PKeidel\Server\DNS\Packet\HeaderFlags::parse(0x0100);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );
    }

    public function testHeaderFlagsParsing02() {
        $headerFlags = PKeidel\Server\DNS\Packet\HeaderFlags::parse(0x8180);
        self::assertEquals(
            'HeaderFlags[QR=response, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Desired, RA=Recursion Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );
    }

    public function testHeaderFlagsSetQr() {
        $headerFlags = PKeidel\Server\DNS\Packet\HeaderFlags::parse(0);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );

        $headerFlags->setQR(1);
        self::assertEquals(
            'HeaderFlags[QR=response, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );
    }

    public function testHeaderFlagsSetOpcode() {
        $headerFlags = PKeidel\Server\DNS\Packet\HeaderFlags::parse(0);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );

        $headerFlags->setOpCode(1);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=IQUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );

        $headerFlags->setOpCode(2);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=STATUS, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );

        $headerFlags->setOpCode(3);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=UNKNOWN: 3, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );
    }

    public function testHeaderFlagsSetAa() {
        $headerFlags = PKeidel\Server\DNS\Packet\HeaderFlags::parse(0);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );

        $headerFlags->setAA(1);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );
    }

    public function testHeaderFlagsSetTc() {
        $headerFlags = PKeidel\Server\DNS\Packet\HeaderFlags::parse(0);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );

        $headerFlags->setTC(1);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );
    }

    public function testHeaderFlagsSetRd() {
        $headerFlags = PKeidel\Server\DNS\Packet\HeaderFlags::parse(0);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );

        $headerFlags->setRD(1);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );
    }

    public function testHeaderFlagsSetRa() {
        $headerFlags = PKeidel\Server\DNS\Packet\HeaderFlags::parse(0);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );

        $headerFlags->setRA(1);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );
    }

    public function testHeaderFlagsSetZ() {
        $headerFlags = PKeidel\Server\DNS\Packet\HeaderFlags::parse(0);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );

        $headerFlags->setZ(1);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=1, RCODE=No error]',
            $headerFlags->__toString()
        );
    }

    public function testHeaderFlagsSetRcode() {
        $headerFlags = PKeidel\Server\DNS\Packet\HeaderFlags::parse(0);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=No error]',
            $headerFlags->__toString()
        );

        $headerFlags->setRCode(1);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=Format error]',
            $headerFlags->__toString()
        );

        $headerFlags->setRCode(2);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=Server failure]',
            $headerFlags->__toString()
        );

        $headerFlags->setRCode(3);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=Name Error]',
            $headerFlags->__toString()
        );

        $headerFlags->setRCode(4);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=Not Implemented]',
            $headerFlags->__toString()
        );

        $headerFlags->setRCode(5);
        self::assertEquals(
            'HeaderFlags[QR=query, Opcode=QUERY, AA=None Authoritative Answer, TC=Not Truncated, RD=Recursion Not Desired, RA=Recursion Not Available, Z=0, RCODE=Refused]',
            $headerFlags->__toString()
        );
    }

}
