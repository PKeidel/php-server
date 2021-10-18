<?php

namespace PKeidel\Server\DHCP;

class Request {

    public static function parse($buffer) {
        $byteArray = unpack('Cop/Chtype/Chlen/Chops/Nxid/nsecs/nflags/Nciaddr/Nyiaddr/Nsiaddr/Ngiaddr/C16mac/C64sname/C128file/C4magiccookie/C*options', $buffer);
        print_r($byteArray);
        return NULL;
    }

}
