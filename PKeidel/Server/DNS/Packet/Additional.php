<?php

namespace PKeidel\Server\DNS\Packet;

class Additional extends Data {
    public $isAdditional = true;
    public $name;
    public $payloadsize;
    public $higherBitsInExtendedRcode;
    public $ednsoVersion;
    public $z;
    public $dataLen;
    public $data;

    public static function fromArray($arr, &$startIndex) {
        $a = new Additional();
        $a->name                      = $arr[$startIndex];
        $a->type                      = ($arr[$startIndex + 1] << 8) | ($arr[$startIndex + 2]);
        $a->payloadsize               = ($arr[$startIndex + 3] << 8) | ($arr[$startIndex + 4]);
        $a->higherBitsInExtendedRcode = $arr[$startIndex + 5];
        $a->ednsoVersion              = $arr[$startIndex + 6];
        $a->z                         = ($arr[$startIndex + 7] << 8) | ($arr[$startIndex + 8]);
        $a->dataLen                   = ($arr[$startIndex + 9] << 8) | ($arr[$startIndex + 10]);

        // if the TYPE is A and the CLASS is IN, the RDATA field is a 4 octet ARPA Internet address
        $a->data                      = array_slice($arr, $startIndex + 11, $a->dataLen);

        $startIndex += 11 + $a->dataLen + 1;
        return $a;
    }

    public function __toString(): string {
        return "Additional Packet";
    }
}
