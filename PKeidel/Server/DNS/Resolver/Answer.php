<?php

namespace PKeidel\Server\DNS\Resolver;

class Answer {
    public $name; // domain
    public $type;
    public $class;
    public $ttl;
    public $datalen = 4;
    public $data;
    public $dataIsIp = false;

    public function __toString(): string {
        $t = [1 => 'A'][$this->type] ?? $this->type;
        if($this->dataIsIp)
            return "Answer for type $t $this->name: ".implode('.', unpack('C*', $this->data));
        return "Answer for type $t $this->name: ".bin2hex($this->data);
    }

    public function toRaw() {
        if($this->dataIsIp) {
            $d = $this->data;
        } else {
            $d = '';
            foreach(explode('.', $this->data) as $dom) {
                $d .= chr(strlen($dom)).$dom;
            }
            $d .= chr(0);
        }

        $tmp = hex2bin("c00c").pack('nnNn', $this->type, $this->class, $this->ttl, $this->datalen).$d;
        return $tmp;
    }
}
