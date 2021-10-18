<?php

namespace PKeidel\Server\DNS\Packet;

class Additional extends Resource {

    public function __toString(): string {
        $infos = explode('\\', static::class);
        if($this->type === 41) {
            return "[ !OPT Pseudo-RR! " . last($infos) . " {UDP payload size={$this->class}, extended RCODE and flags=$this->ttl, rdlength=$this->datalen, rdata=$this->dataRaw}]";
        }
        return "[" . last($infos) . " {domain={$this->domain}, type={$this->getTypeStr()}, class={$this->getClassStr()}, ttl=$this->ttl, rdlength=$this->datalen, rdata=$this->dataReadable}]";
    }

    public function toRaw() {
        $d = '';
//        foreach(explode('.', $this->domain) as $dom) {
//            $d .= chr(strlen($dom)).$dom;
//        }
        $d .= chr(0);

        return $d.pack('nnNn', $this->type, $this->class, $this->ttl, $this->datalen).$this->dataRaw;
    }
}
