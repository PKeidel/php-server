<?php

namespace PKeidel\Server\DNS\Packet;

use PKeidel\Server\DNS\Resolver\IP;

abstract class Resource {
    public string $domain;
    public int $type;
    public int $class;
    public int $ttl;
    public int $datalen = 0;
    public string $dataRaw = '';
    public string $dataReadable = '';

    private const DNS_TYPES = [
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
        28 => 'AAAA',
    ];

    private const DNS_CLASSES = [
        1 => 'IN',
        2 => 'CS',
        3 => 'CH',
        4 => 'HS'
    ];

    public static function parse(ArrayReader $arrayReader, int &$nextIndex): Resource {
        $res = new static();
        $res->domain = $arrayReader->readNullTerminated($nextIndex);
        $res->type = $arrayReader->read2ByteInt($nextIndex);

        $res->class = $arrayReader->read2ByteInt($nextIndex);
        $res->ttl = $arrayReader->read4ByteInt($nextIndex);

        $res->datalen = $arrayReader->read2ByteInt($nextIndex);
        $dataStartIndex = $nextIndex;
        $res->dataRaw = $arrayReader->readBytes($nextIndex, $res->datalen);

        if($res->datalen > 0) {
            try {
                switch ($res->getTypeStr()) {
                    case 'A':
                        $res->dataReadable = IP::bin2string($res->dataRaw);
                        break;
                    case 'AAA':
                        $res->dataReadable = $arrayReader->readBytes($dataStartIndex, $res->datalen);
                        break;
                    case 'TXT':
                        $len = $arrayReader->read1ByteInt($dataStartIndex);
                        $res->dataReadable = $arrayReader->readBytes($dataStartIndex, $len);
                        break;
                    default:
                        $res->dataReadable = $arrayReader->readNullTerminated($dataStartIndex);
                        break;
                }
            } catch (\Throwable $t) {
                $res->dataReadable = $t::class . ': ' . $t->getMessage();
            }
        }

        return $res;
    }

    public function __toString(): string {
        $infos = explode('\\', static::class);
        return "[" . last($infos) . " {domain={$this->domain}, type={$this->getTypeStr()}, class={$this->getClassStr()}, ttl=$this->ttl, rdlength=$this->datalen, rdata=$this->dataReadable}]";
    }

    abstract public function toRaw();

    public function getTypeStr(): string {
        return self::DNS_TYPES[$this->type] ?? 0xFF;
    }

    public function getClassStr(): string {
        return self::DNS_CLASSES[$this->class] ?? 0xFF;
    }
}
