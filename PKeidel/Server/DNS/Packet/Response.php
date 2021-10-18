<?php

namespace PKeidel\Server\DNS\Packet;

use PKeidel\Server\DNS\Resolver\Answer;
use PKeidel\Server\UDP\UDPPacket;

class Response extends UDPPacket {
//    /** @var int */
//    public $transactionID;
//    /** @var HeaderFlags */
//    public $flags;
//    /** @var int */
//    public $qdCount;
//    /** @var int */
//    public $anCount;
//    /** @var int */
//    public $nsCount;
//    /** @var int */
//    public $arCount;
//    /** @var Data[] */
//    public $questions;
//    /** @var Answer[] */
//    public $answers;

    public static function fromRequest(DNSPacket $request): Response {
        $dnsRes = new self($request->getRaw());
        $dnsRes->txid = $request->txid;
//        $dnsRes->flags = $request->flags;
//        $dnsRes->flags->markAsResponse();
//        $dnsRes->qdCount = $request->qdCount;
//        $dnsRes->anCount = 0;
//        $dnsRes->nsCount = 0;
//        $dnsRes->arCount = 0;
//
//        $dnsRes->questions = $request->qd;
dd($dnsRes);
        return $dnsRes;
    }

    /** @param Answer[] $answers */
    public function setAnswers(array $answers): void {
        $this->answers = $answers;
        $this->anCount = count($answers);

        if($this->anCount === 0) {
            $this->flags->setRCode(3);
        }
    }

    /** Creates the binary string to send back to the client */
    public function toRaw(): string {
        dump("toRaw()", $this);
        $questions = implode('', array_map(function(Data $q) {
            return $q->toRaw();
        }, $this->questions));
        $answ = implode('', array_map(function(Answer $a) {
            return $a->toRaw();
        }, $this->answers));
        return pack('nnnnnn', $this->transactionID, $this->flags->flags, $this->qdCount, $this->anCount, $this->nsCount, $this->arCount).$questions.$answ;
    }

    public function parseData() {
        // TODO: Implement parseData() method.
    }

    public function getFormat() {
        // TODO: Implement getFormat() method.
    }
}
