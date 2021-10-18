<?php

namespace PKeidel\Server\DNS\Packet;

// https://tools.ietf.org/html/rfc1035#section-4.1.1

class HeaderFlags {
    public int $flags = 0;

    private const SHIFT_COUNT_QR = 15;
    private const SHIFT_COUNT_OPODE = 11;
    private const SHIFT_COUNT_AA = 10;
    private const SHIFT_COUNT_TC = 9;
    private const SHIFT_COUNT_RD = 8; // Recursion Desired
    private const SHIFT_COUNT_RA = 7; // Recursion Available
    private const SHIFT_COUNT_Z = 4;

    private const FLAG_QR = 1 << self::SHIFT_COUNT_QR;
    private const FLAG_OPCODE = 0b1111 << self::SHIFT_COUNT_OPODE;
    private const FLAG_AA = 1 << self::SHIFT_COUNT_AA;
    private const FLAG_TC = 1 << self::SHIFT_COUNT_TC;
    private const FLAG_RD = 1 << self::SHIFT_COUNT_RD;
    private const FLAG_RA = 1 << self::SHIFT_COUNT_RA;
    private const FLAG_Z = 0b111 << self::SHIFT_COUNT_Z;
    private const FLAG_RCODE = 0b1111;

    public const OPCODE_0 = 'QUERY';
    public const OPCODE_1 = 'IQUERY';
    public const OPCODE_2 = 'STATUS';

    public const RCODE_0 = 'No error';
    public const RCODE_1 = 'Format error'; // The name server was unable to interpret the query
    public const RCODE_2 = 'Server failure'; // The name server was unable to process this query due to a problem with the name server
    public const RCODE_3 = 'Name Error'; // Meaningful only for responses from an authoritative name server, this code signifies that the domain name referenced in the query does not exist
    public const RCODE_4 = 'Not Implemented'; // The name server does not support the requested kind of query
    public const RCODE_5 = 'Refused'; // The name server refuses to perform the specified operation for policy reasons.  For example, a name server may not wish to provide the information to the particular requester,  or a name server may not wish to perform a particular operation (e.g., zone transfer) for particular data

    public static function parse(int $flags): HeaderFlags {
        $f = new self();
        $f->flags = $flags;
        return $f;
    }

    public function __toString(): string {
        $qr = $this->getQRString();
        $opCode = $this->getOpCodeString();
        $aa = $this->getAAString();
        $tc = $this->getTCString();
        $rd = $this->getRDString();
        $ra = $this->getRAString();
        $z = $this->getZ();
        $rCode = $this->getRCodeString();
        return "HeaderFlags[QR=$qr, Opcode=$opCode, AA=$aa, TC=$tc, RD=$rd, RA=$ra, Z=$z, RCODE=$rCode]";
    }

    public function getQR(): int {
        return ($this->flags & self::FLAG_QR) >> self::SHIFT_COUNT_QR;
    }
    public function getQRString(): string {
        return $this->getQR() === 0 ? 'query' : 'response';
    }
    public function isQuery(): bool {
        return $this->getQR() === 0;
    }
    public function isResponse(): bool {
        return !$this->isQuery();
    }
    public function markAsQuery() {
        $this->setQR(0);
    }
    public function markAsResponse() {
        $this->setQR(1);
    }
    public function setQR($qr) {
        $this->flags = ($this->flags & ~self::FLAG_QR) | ($qr << self::SHIFT_COUNT_QR);
    }

    /** 0=Query; 1=Inverse Query; 2=Status; 3=available for assignment; 4=Notify; 5=Update; 6-15=available for assignment */
    public function getOpCode(): int {
        return ($this->flags & self::FLAG_OPCODE) >> self::SHIFT_COUNT_OPODE;
    }
    /** @param $opcode 0=Query; 1=Inverse Query; 2=Status; 3=available for assignment; 4=Notify; 5=Update; 6-15=available for assignment */
    public function setOpCode($opcode) {
        $this->flags = ($this->flags & ~self::FLAG_OPCODE) | ($opcode << self::SHIFT_COUNT_OPODE);
    }
    public function getOpCodeString(): string {
        return match($o = $this->getOpCode()) {
            0 => static::OPCODE_0,
            1 => static::OPCODE_1,
            2 => static::OPCODE_2,
            default => 'UNKNOWN: ' . $o
        };
    }

    public function getAA(): int {
        return ($this->flags & self::FLAG_AA) >> self::SHIFT_COUNT_AA;
    }
    public function setAA($aa) {
        $this->flags = ($this->flags & ~self::FLAG_AA) | ($aa << self::SHIFT_COUNT_AA);
    }
    public function getAAString(): string {
        return ($this->getAA() === 0 ? 'None ' : '') . 'Authoritative Answer';
    }

    public function getTC(): int {
        return ($this->flags & self::FLAG_TC) >> self::SHIFT_COUNT_TC;
    }
    public function setTC($tc) {
        $this->flags = ($this->flags & ~self::FLAG_TC) | ($tc << self::SHIFT_COUNT_TC);
    }
    public function getTCString(): string {
        return ($this->getTC() === 0 ? 'Not ' : '') . 'Truncated';
    }

    public function getRD(): int {
        return ($this->flags & self::FLAG_RD) >> self::SHIFT_COUNT_RD;
    }
    public function setRD(int $rd) {
        $this->flags = ($this->flags & ~self::FLAG_RD) | ($rd << self::SHIFT_COUNT_RD);
    }
    public function getRDString(): string {
        return match($this->getRD()) {
            0 => 'Recursion Not Desired',
            1 => 'Recursion Desired',
        };
    }

    public function getRA(): int {
        return ($this->flags & self::FLAG_RA) >> self::SHIFT_COUNT_RA;
    }
    public function setRA(int $ra) {
        $this->flags = ($this->flags & ~self::FLAG_RA) | ($ra << self::SHIFT_COUNT_RA);
    }
    public function getRAString(): string {
        return match($this->getRA()) {
            0 => 'Recursion Not Available',
            1 => 'Recursion Available',
        };
    }

    public function getZ(): int {
        return ($this->flags & self::FLAG_Z) >> self::SHIFT_COUNT_Z;
    }
    public function setZ($z) {
        $this->flags = ($this->flags & ~self::FLAG_Z) | ($z << self::SHIFT_COUNT_Z);
    }

    public function getRCode(): int {
        return ($this->flags & self::FLAG_RCODE);
    }
    public function setRCode($rcode) {
        $this->flags = ($this->flags & ~self::FLAG_RCODE) | $rcode;
    }
    public function getRCodeString(): string {
        return match($r = $this->getRCode()) {
            0 => static::RCODE_0,
            1 => static::RCODE_1,
            2 => static::RCODE_2,
            3 => static::RCODE_3,
            4 => static::RCODE_4,
            5 => static::RCODE_5,
            default => 'UNKNOWN: ' . $r
        };
    }

    public function getAsBinary(): string {
        return pack('n', $this->flags);
    }

    public function getAsHex(): string {
        return bin2hex($this->getAsBinary());
    }
}
