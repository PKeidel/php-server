<?php

namespace PKeidel\Server\DNS\Packet;

// https://datatracker.ietf.org/doc/html/rfc1035#section-4.1.2
class Question extends Resource {

    public static function parse(ArrayReader $arrayReader, int &$nextIndex): Question {
        $q = new static();
        $q->domain = $arrayReader->readNullTerminated($nextIndex);
        $q->type = $arrayReader->read2ByteInt($nextIndex);
        $q->class = $arrayReader->read2ByteInt($nextIndex);
        return $q;
    }

    public function toRaw(): string {
        $d = '';
        foreach(explode('.', $this->domain) as $dom) {
            $d .= chr(strlen($dom)).$dom;
        }
        $d .= chr(0);

        return $d.pack('nn', $this->type, $this->class);
    }

    public function __toString(): string {
        $infos = explode('\\', static::class);
        return "[" . last($infos) . " {type={$this->getTypeStr()}, class={$this->getClassStr()}, domain={$this->domain}}]";
    }
}
