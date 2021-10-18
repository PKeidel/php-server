<?php

namespace PKeidel\Server\DNS\Packet;

class Response extends DNSPacket {
    /** @var Resource[] */
    public array $answers = [];

    public static function fromRequest(DNSPacket $request) {
        $dnsRes = new self($request->toRaw());
        $dnsRes->flags->markAsResponse();
        $dnsRes->anCount = 0;
        $dnsRes->nsCount = 0;
        $dnsRes->arCount = 0;

        return $dnsRes;
    }

    /** @param Resource[] $answers */
    public function setAnswers(array $answers): void {
        $this->answers = $answers;
        $this->anCount = count($answers);
    }

    /** Creates the binary string to send back to the client */
    public function toRaw(): string {
        $questions = implode('', array_map(function(Question $q) {
            return $q->toRaw();
        }, $this->qd));
        $answ = implode('', array_map(function(Resource $a) {
            return $a->toRaw();
        }, $this->answers));
        return pack('nnnnnn', $this->txid, $this->flags->flags, $this->qdCount, $this->anCount, $this->nsCount, $this->arCount).$questions.$answ;
    }

}
