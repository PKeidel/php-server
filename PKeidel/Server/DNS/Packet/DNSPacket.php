<?php

namespace PKeidel\Server\DNS\Packet;

use PKeidel\Server\UDP\UDPPacket;

class DNSPacket extends UDPPacket {
    public int $txid = 0;
    public HeaderFlags $flags;
    public int $qdCount = 0;
    public int $anCount = 0;
    public int $nsCount = 0;
    public int $arCount = 0;

    /** @var Question[] */
    public array $qd = [];
    /** @var Resource[] */
    public array $an = [];
    /** @var array[] */
    public array $ns = [];
    /** @var array[] */
    public array $ar = [];

    public function parseData(): DNSPacket {
        $nextIndex = 1; // data from unpack starts with index 1

        // Header section format: https://datatracker.ietf.org/doc/html/rfc1035#section-4.1.1
        $this->txid = $this->arrayReader->read2ByteInt($nextIndex);
        $this->flags = HeaderFlags::parse($this->arrayReader->read2ByteInt($nextIndex));
        $this->qdCount = $this->arrayReader->read2ByteInt($nextIndex);
        $this->anCount = $this->arrayReader->read2ByteInt($nextIndex);
        $this->nsCount = $this->arrayReader->read2ByteInt($nextIndex);
        $this->arCount = $this->arrayReader->read2ByteInt($nextIndex);

        // number of entries in the question section
        // https://datatracker.ietf.org/doc/html/rfc1035#section-4.1.2
        for($i = 0; $i < $this->qdCount; $i++) {
//            echo "Parsing Question #$i beginning at position $nextIndex\n";
            $this->qd[] = Question::parse($this->arrayReader, $nextIndex);
        }

        // number of resource records in the answer section
        for($i = 0; $i < $this->anCount; $i++) {
//            echo "Parsing Answer #$i beginning at position $nextIndex\n";
            $this->an[] = Answer::parse($this->arrayReader, $nextIndex);
        }

        // number of resource records in the authority section
        for($i = 0; $i < $this->nsCount; $i++) {
//            echo "Parsing Authority #$i beginning at position $nextIndex\n";
            $this->ns[] = Authority::parse($this->arrayReader, $nextIndex);
        }

        // number of resource records in the additional section
        for($i = 0; $i < $this->arCount; $i++) {
//            echo "Parsing Additional #$i beginning at position $nextIndex\n";
            $this->ar[] = Additional::parse($this->arrayReader, $nextIndex);
        }

        return $this;
    }

    /** @param Question[] $questions */
    public function setQuestions(array $questions): void {
        $this->qd = $questions;
        $this->qdCount = count($questions);
    }

    /** @param Resource[] $answers */
    public function setAnswers(array $answers): void {
        $this->an = $answers;
        $this->anCount = count($answers);
    }

    /** @param Resource[] $additionals */
    public function setAdditional(array $additionals): void {
        $this->ar = $additionals;
        $this->arCount = count($additionals);
    }

    /** Creates the binary string to send back to the client */
    public function toRaw(): string {
        $questions = implode('', array_map(function(Question $q) {
            return $q->toRaw();
        }, $this->qd));
        $answ = implode('', array_map(function(Resource $a) {
            return $a->toRaw();
        }, $this->an));
        $authority = implode('', array_map(function(Resource $a) {
            return $a->toRaw();
        }, $this->ns));
        $additional = implode('', array_map(function(Resource $a) {
            return $a->toRaw();
        }, $this->ar));
        return pack('nnnnnn', $this->txid, $this->flags->flags, $this->qdCount, $this->anCount, $this->nsCount, $this->arCount).$questions.$answ.$authority.$additional;
    }

    public function __toString(): string {
        return sprintf(
            "[DNSPacket {\n\tflags=%s,\n\ttxid=%s, qdCount=%d, anCount=%d, nsCount=%d, arCount=%d,\n\tqd=%s,\n\tan=%s,\n\tns=%s,\n\tar=%s\n}]",
            $this->flags->__toString(),
            dechex($this->txid),
            $this->qdCount,
            $this->anCount,
            $this->nsCount,
            $this->arCount,
            implode(', ', array_map(fn(Question $qd) => $qd->__toString(), $this->qd)),
            implode(', ', array_map(fn(Resource $an) => $an->__toString(), $this->an)),
            implode(', ', array_map(fn($ns) => $ns->__toString(), $this->ns)),
            implode(', ', array_map(fn($ar) => $ar->__toString(), $this->ar)),
        );
    }
}
