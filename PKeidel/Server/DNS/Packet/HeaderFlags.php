<?php

namespace PKeidel\Server\DNS\Packet;

// https://tools.ietf.org/html/rfc1035#section-4.1.1

class HeaderFlags {
    public int $flags = 0;

    public static function parse(int $flags) {
        $f = new self();
        $f->flags = $flags;
        return $f;
    }

    public function getRA() {
        return ($this->flags & (1<<7)) >> 7;
    }
    public function setRA(int $ra) {
        $this->flags = ($this->flags & ~(1<<7)) | ($ra << 7);
    }

    public function isQuery(): bool {
        // query if 1. bit == 0
        return ($this->flags & (1<<15)) === 0;
    }

    public function isResponse(): bool {
        return !$this->isQuery();
    }
    public function markAsQuery() {
        $this->flags = $this->flags & ~(1<<15);
    }
    public function markAsResponse() {
        $this->flags = $this->flags | (1<<15);
    }

    /** 0=Query; 1=Inverse Query; 2=Status; 3=available for assignment; 4=Notify; 5=Update; 6-15=available for assignment */
    public function getOpCode() {
        return ($this->flags & (15<<11)) >> 11;
    }
    /** @param $opcode 0=Query; 1=Inverse Query; 2=Status; 3=available for assignment; 4=Notify; 5=Update; 6-15=available for assignment */
    public function setOpCode($opcode) {
        $this->flags = ($this->flags & ~(15<<11)) | ($opcode << 11);
    }

    public function getRCode() {
        return $this->flags & 15;
    }
    public function setRCode($rcode) {
        $this->flags = ($this->flags & ~15) | $rcode;
    }

    public function getRD() {
        return ($this->flags & (1<<8)) >> 8;
    }
    public function setRD(int $rd) {
        $this->flags = ($this->flags & ~(1<<8)) | ($rd << 8);
    }

    public function getAsBinary() {
        return pack('n', $this->flags);
    }

    public function getAsHex() {
        return bin2hex($this->getAsBinary());
    }
}
