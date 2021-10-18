<?php

namespace PKeidel\Server\DNS\Packet;

use PKeidel\Server\UDP\UDPPacket;

/**
 * Class DNSPacket
 *
 * @property int txid
 * @property HeaderFlags flags
 * @property int qdCount
 * @property int anCount
 * @property int nsCount
 * @property int arCount
 * @property array qd
 * @property array an
 * @property array ns
 * @property array ar
 *
 * @package PKeidel\Server\DNS
 */
class DNSPacket extends UDPPacket {

    /** @var Query[] */
    public $qd = [];
    /** @var array[] */
    public $an = [];
    /** @var array[] */
    public $ns = [];
    /** @var array[] */
    public $ar = [];

    public function getFormat() {
        return 'ntxid/nflags/nqdCount/nanCount/nnsCount/narCount/C*c';
    }

    public function parseData() {

        $this->flags = HeaderFlags::parse($this->flags);

        $nextIndex = 0;

        // number of entries in the question section
        if($this->qdCount) {
//            echo "reading $this->qdCount entries of question section ...\n";
            for($i = 0; $i < $this->qdCount; $i++) {
                $this->qd[] = Query::fromArray($this->c, $nextIndex);
            }
        }

//        // number of resource records in the answer section
//        if($this->anCount) {
//            echo "reading $this->anCount resource records of answer section ...\n";
//            for($i = 0; $i < $this->anCount; $i++) {
//                $nextIndex = $this->readNullTerminated($nextIndex, $domain);
//                $this->an[] = Query::fromDomain($domain);
//            }
//        }
//
//        // number of name server resource in the authority records section
//        if($this->nsCount) {
//            echo "reading $this->nsCount server resources of authority records section ...\n";
//            for($i = 0; $i < $this->nsCount; $i++) {
//                $nextIndex = $this->readNullTerminated($nextIndex, $domain);
//                $this->ns[] = Query::fromDomain($domain);
//            }
//        }

        // number of additional records in the additional records section
        if($this->arCount) {
//            echo "reading $this->arCount additional records of additional records section ...\n";
            for($i = 0; $i < $this->arCount; $i++) {
//                $nextIndex = $this->readNullTerminated($nextIndex, $domain);
//                $this->ar[] = Query::fromDomain($domain);
                $this->ar[] = Additional::fromArray($this->c, $nextIndex);
            }
        }

        return $this;
    }

}
