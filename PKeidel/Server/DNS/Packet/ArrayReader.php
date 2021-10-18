<?php

namespace PKeidel\Server\DNS\Packet;

class ArrayReader {
    private array $buffer;
    private const IS_POINTER_FLAG = 0b11<<6;

    public function __construct(array $buf) {
        $this->buffer = $buf;
    }

    public function readBytes(int &$startPosition, int $bytesToRead): string {
        $first = $startPosition;
        $data = '';
        for($i = 0; $i < $bytesToRead; $i++) {
            if(!array_key_exists($startPosition, $this->buffer)) {
                throw new \Exception("ArrayReader::readBytes($startPosition) not possible. Key doesn't exist in buffer; data=$data");
            }
            $data .= chr($this->buffer[$startPosition++]);
        }
        return $data;
    }

    public function readNullTerminated(int &$startPosition): string {
        $domain = '';
        while(true) {
            $bytesToRead = $this->buffer[$startPosition++];
            if ($bytesToRead === 0)
                break;

            if(strlen($domain) > 0) $domain .= '.';

            if($this->isPointer($bytesToRead)) {
                $newStartPosition = 1 /* unpack starts with index 1 */ + ($bytesToRead << 8 | $this->buffer[$startPosition++]) & 0b0011111111111111;
                $domain .= $this->readNullTerminated($newStartPosition);
                return $domain;
            } else {
                $domain .= $this->readBytes($startPosition, $bytesToRead);
            }
        }

        return $domain;
    }

    private function isPointer(int $bytesToRead): bool {
        return ($bytesToRead & self::IS_POINTER_FLAG) === self::IS_POINTER_FLAG;
    }

    public function read1ByteInt(int &$startPosition): int {
        return $this->buffer[$startPosition++];
    }

    public function read2ByteInt(int &$startPosition): int {
        return $this->buffer[$startPosition++] << 8 | $this->buffer[$startPosition++];
    }

    public function read4ByteInt(int &$startPosition): int {
        return $this->buffer[$startPosition++] << 24 | $this->buffer[$startPosition++] << 16 | $this->buffer[$startPosition++] << 8 | $this->buffer[$startPosition++];
    }
}
