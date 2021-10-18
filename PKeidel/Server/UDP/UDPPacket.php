<?php

namespace PKeidel\Server\UDP;

abstract class UDPPacket {
    /** @var string */
    private $raw;
    private $data;

    public function __construct(string $buffer) {
        $this->raw = $buffer;
        $byteArray = unpack($this->getFormat(), $buffer);

        /*
        Array
        (
            [txid] => 22751
            [flags] => 288
            [qdCount] => 1
            [anCount] => 0
            [nsCount] => 0
            [arCount] => 1
            [c1] => 6
            [c2] => 103
            ...
        )
         */

        foreach($byteArray as $key => $value) {
            // Check if $key ends with a number
            preg_match("/([a-z]+)([0-9]+)?/", $key, $matches);
            // if named single value
            if(count($matches) === 2) {
                $this->data[$key] = $value;

            // if named array
            } else {
                $this->data[$matches[1]][] = $value;
            }
        }

        $this->parseData();
    }

    public abstract function parseData();

    public abstract function getFormat();

    public function getRaw() {
        return $this->raw;
    }

    /**
     * Allows to access the data in the format $packet->txid
     * @param $key
     * @return mixed
     */
    public function __get($key) {
        return $this->data[$key] ?? NULL;
    }
}
