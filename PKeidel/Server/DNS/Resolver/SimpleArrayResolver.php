<?php

namespace PKeidel\Server\DNS\Resolver;

use PKeidel\Server\DNS\Packet\DNSPacket;

class SimpleArrayResolver implements IDnsResolver {
    /**
     * @param DNSPacket $request
     * @return Resource[]
     */
    public function getAnswersFor(DNSPacket $request): array {
        $answers = [];

        foreach($request->qn as $question) {

            if($question->domain[0] === 'open' && $question->domain[1] === 'spotify' && $question->domain[2] === 'com') {

                $answer1 = new Resource();
                $answer1->domain = 'open.spotify.com';
                $answer1->type = 5; // 5=CNAME
                $answer1->class = 1;
                $answer1->ttl   = 57; // in seconds
                $answer1->datalen = 32;
                $answer1->data  = 'edge-web.dual-gslb.spotify.com';
                $answers[] = $answer1;

                $answer2 = new Resource();
                $answer2->domain = 'edge-web.dual-gslb.spotify.com';
                $answer2->type = 1;
                $answer2->class = 1;
                $answer2->ttl   = 85; // in seconds
                $answer2->datalen = 4;
                $answer2->data  = IP::readableToRaw(35, 186, 224, 30);
                $answers[] = $answer2;

            } else {
                foreach(range(1, 4) as $i) {
                    $answer = new Resource();
                    $answer->domain = implode('.', $question->domain); // TODO
                    $answer->type = 1; // TODO
                    $answer->class = 1; // TODO
                    $answer->ttl   = 1000 + $i; // in seconds
                    $answer->data  = IP::readableToRaw(10, 10, 1, 100 + $i); // TODO
                    $answers[] = $answer;
                }
            }
        }

        return $answers;
    }
}
