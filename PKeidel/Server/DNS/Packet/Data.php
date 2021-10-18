<?php

namespace PKeidel\Server\DNS\Packet;

abstract class Data {

    /** @var string[] */
    public $domain = [];
    /** @var int 1=A; 2=NS; 3=MD; 4=MF; 5=CNAME; 6=SOA; 7=MB; 8=MG; 9=MR; 10=NULL; 11=WKS; 12=PTR; 13=HINFO; 14=MINFO; 15=MX; 16=TXT */
    public $type;
    /** @var int 1=IN; 2=CS; 3=CH; 4=HS */
    public $class;
    public $ttl;
    public $rdlength;
    public $rdata;

    public $isQuery = false;
    public $isAnswer = false;
    public $isAuthority = false;
    public $isAdditional = false;

//    /**
//     * @param array $byteArray
//     * @param int $qdCount
//     * @param int $anCount
//     * @param int $nsCount
//     * @param int $arCount
//     * @return Data[]
//     */
//    public static function parse(array $byteArray, int $qdCount, int $anCount, int $nsCount, int $arCount): array {
//        $questions = [];
//
//        $i = 1;
//
//        foreach(range(1, $qdCount) as $read) {
//            $question = new self();
//            // read all domain parts until 0x00
//            while(($len = $byteArray["c$i"]) > 0) {
//                $i++;
//                $readUntil = $i + $len;
//
//                $domainPart = '';
//                while($i < $readUntil) {
//                    $domainPart .= chr($byteArray["c$i"]);
//                    $i++;
//                }
//                $question->domain[] = $domainPart;
//            }
//
//            // Type
//            $i++;
//            $j = $i + 1;
//            $question->type = ($byteArray["c$i"] << 8) | $byteArray["c$j"];
//            $i += 2;
//            $j = $i + 1;
//            $question->class = ($byteArray["c$i"] << 8) | $byteArray["c$j"];
//            $i += 2;
//
//            $questions[] = $question;
//        }
//
//        return $questions;
//    }

    public abstract function __toString(): string;

    public function toRaw() {
        $d = '';
        foreach($this->domain as $dom) {
            $d .= chr(strlen($dom)).$dom;
        }
        $d .= chr(0);
        return $d.pack('nn', $this->type, $this->class);
    }

    public function readNullTerminated(array $arr, $startIndex, &$domain) {
        $domain = '';
        $currentIndex = $startIndex;
        while(true) {
            $readHowMany = $arr[$currentIndex];
//            echo "read $readHowMany bytes\n";
            if(!$readHowMany)
                break;
            $currentIndex++;
            while($readHowMany > 0) {
                $domain .= chr($arr[$currentIndex]);
                $currentIndex++;
                $readHowMany--;
            }
            $domain .= '.';
        }
        if(substr($domain, -1, 1) === '.')
            $domain = substr($domain, 0, -1);
        return $currentIndex + 1;
    }
}
