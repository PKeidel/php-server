<?php

namespace PKeidel\Server\DNS\Packet;

class Answer extends Resource {
    public function toRaw(): string {
        $d = '';
        foreach(explode('.', $this->domain) as $dom) {
            $d .= chr(strlen($dom)).$dom;
        }
        $d .= chr(0);

        return $d.pack('nnNn', $this->type, $this->class, $this->ttl, $this->datalen).$this->dataRaw;
    }
}
