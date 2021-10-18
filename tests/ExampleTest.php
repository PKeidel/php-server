<?php

use PKeidel\DNS\Resolver\SimpleArrayResolver;
use PKeidel\Server\DNS\Server\Server as DNSServer;

class ExampleTest extends TestCase {
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGui() {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }

    public function testDns() {
        $dnsServer = new DNSServer(new SimpleArrayResolver());
        $buffer = hex2bin("b50301000001000000000000046f70656e0773706f7469667903636f6d0000010001");
        $dnsServer->handlePacket("1.2.3.4", 54321, $buffer);
        echo "\n\nErwartete Antwort:\n";
        echo "         ".implode(' ', explode("\n", chunk_split("b50381800001000200000000046f70656e0773706f7469667903636f6d0000010001c00c0005000100000039002008656467652d776562096475616c2d67736c620773706f7469667903636f6d00c02e0001000100000055000423bae01e", 2, "\n")))."\n";

    }
}
