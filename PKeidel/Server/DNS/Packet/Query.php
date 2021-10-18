<?php

namespace PKeidel\Server\DNS\Packet;

class Query extends Data {
    public $isQuery = true;
    public $domain;

    /** type  1=A; 2=NS; 3=MD; 4=MF; 5=CNAME; 6=SOA; 7=MB; 8=MG; 9=MR; 10=NULL; 11=WKS; 12=PTR; 13=HINFO; 14=MINFO; 15=MX; 16=TXT */
    /** class 1=IN; 2=CS; 3=CH; 4=HS */

    public function getTypeStr() {
        return [
            1  => 'A',
            2  => 'NS',
            3  => 'MD',
            4  => 'MF',
            5  => 'CNAME',
            6  => 'SOA',
            7  => 'MB',
            8  => 'MG',
            9  => 'MR',
            10 => 'NULL',
            11 => 'WKS',
            12 => 'PTR',
            13 => 'HINFO',
            14 => 'MINFO',
            15 => 'MX',
            16 => 'TXT',
        ][$this->type] ?? '-';
    }

    public function getClassStr() {
        return [
            1 => 'IN',
            2 => 'CS',
            3 => 'CH',
            4 => 'HS'
        ][$this->class] ?? '-';
    }

    public static function fromArray($arr, &$startIndex) {
        $q = new Query();
        $startIndex = $q->readNullTerminated($arr, $startIndex, $domain);
        $q->domain = $domain;
        $q->type  = ($arr[$startIndex + 0] << 8) | ($arr[$startIndex + 1]);
        $q->class = ($arr[$startIndex + 2] << 8) | ($arr[$startIndex + 3]);
        $startIndex += 4;
        return $q;
    }

    public function __toString(): string {
        $t = [1 => 'A'][$this->type] ?? $this->type;
        $d = implode('.', $this->domain);
        return "Query for type $t of $d";
    }
}
