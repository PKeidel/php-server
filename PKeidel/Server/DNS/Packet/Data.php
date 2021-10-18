<?php

namespace PKeidel\Server\DNS\Packet;

abstract class Data {

    /** @var string[] */
    public $domain = [];
    /** @var int 1=A; 2=NS; 3=MD; 4=MF; 5=CNAME; 6=SOA; 7=MB; 8=MG; 9=MR; 10=NULL; 11=WKS; 12=PTR; 13=HINFO; 14=MINFO; 15=MX; 16=TXT */
    public int $type;
    /** @var int 1=IN; 2=CS; 3=CH; 4=HS */
    public int $class;
    public $ttl;
    public $rdlength;
    public $rdata;

    public bool $isQuery = false;
    public bool $isAnswer = false;
    public bool $isAuthority = false;
    public bool $isAdditional = false;

    public abstract function __toString(): string;

    public function toRaw() {

        if(is_string($this->domain)) {
            $this->domain = explode('.', $this->domain);
        }

        $d = '';
        foreach($this->domain as $dom) {
            $d .= chr(strlen($dom)).$dom;
        }
        $d .= chr(0);
        return $d.pack('nn', $this->type, $this->class);
    }
}
