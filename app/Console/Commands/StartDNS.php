<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PKeidel\Server\DNS\Client\Client;
use PKeidel\Server\DNS\Packet\DNSPacket;
use PKeidel\Server\DNS\Packet\Query;
use PKeidel\Server\DNS\Resolver\Answer;
use PKeidel\Server\DNS\Resolver\IDnsResolver;
use PKeidel\Server\DNS\Resolver\IP;
use PKeidel\Server\DNS\Server\Server as DNSServer;

class StartDNS extends Command implements IDnsResolver {
    protected $signature = 'server:startdns {ip?} {--onlyprintip}';
    protected $description = 'Reads all configured Mailboxes and saves the Mails to the Database';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle(): void {

        $port = 53;

        if($this->option('onlyprintip')) {
            die($this->getExternalIp($port));
        }

        $this->info("StartDNS::handle()");

        $ip = $this->argument('ip') ?? $this->getExternalIp($port);

        echo "listening on ip $ip:$port\n";

        $dnsServer = new DNSServer($this);

        $dnsServer->start($ip, $port);
    }

    private function getExternalIp($port): string {
        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_connect($sock, "8.8.8.8", $port);
        socket_getsockname($sock, $ip);
        return $ip;
    }

    /**
     * @inheritDoc
     */
    public function getAnswersFor(DNSPacket $request): array {
        $answers = [];

        /** @var Query $question */
        foreach($request->qd as $question) {
            $dnsrecords = app('db')->select("SELECT * FROM dnszones WHERE type = ? AND class = ? AND host = ?", [$question->getTypeStr(), $question->getClassStr(), $question->domain]);
            foreach($dnsrecords as $dnsrecord) {
                $answer = new Answer();
                $answer->name     = $dnsrecord->host;
                $answer->type     = $dnsrecord->type;
                $answer->class    = $dnsrecord->class;
                $answer->ttl      = $dnsrecord->ttl;
                $answer->dataIsIp = true;
                $answer->data     = IP::readableArrToRaw(explode('.', $dnsrecord->ip));
                $answers[]        = $answer;
            }
        }

        if(count($answers) === 0) {
            echo "  => fetch records from 3rd party DNS Server\n";
            // fetch records from 3rd party DNS Server
            $dnsClient = new Client();
            $results = $dnsClient->askForRaw('192.168.0.1', $request->getRaw());
            // cache records
            foreach($results as $result) {
                try {
                    app('db')->insert('INSERT INTO dnszones (host, ip, class, type, ttl) VALUES ()', [
                        $result->name,
                        $result->data, // TODO convert
                        $result->class,
                        $result->type,
                        $result->ttl
                    ]);
                } catch (\Exception $e) {
                    Log::error($e);
                }
                $answers[] = $result;
            }
        }

        return $answers;
    }
}
