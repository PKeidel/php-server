<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PKeidel\Server\DNS\Client\Client;
use PKeidel\Server\DNS\Packet\Answer;
use PKeidel\Server\DNS\Packet\DNSPacket;
use PKeidel\Server\DNS\Packet\Resource;
use PKeidel\Server\DNS\Resolver\IDnsResolver;
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

        $port = (int)(env('PORT') ?? '53');

        if($this->option('onlyprintip')) {
            die($this->getExternalIp($port));
        }

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

    public function getAnswersFor(DNSPacket $request): array {
        $answers = [];

        if(count($request->qd) > 0)
            echo "│   └── fetch records from local database\n";

        foreach($request->qd as $question) {
            $dnsrecords = app('db')->select("SELECT * FROM dnszones WHERE type = ? AND class = ? AND host = ?", [$question->getTypeStr(), $question->getClassStr(), $question->domain]);
            if(count($dnsrecords) > 0)
                echo "│   └── fetch records from local database\n";
            foreach($dnsrecords as $dnsrecord) {
                $answer = new Answer();
                $answer->domain     = $dnsrecord->host;
                $answer->type     = ['A' => 1][$dnsrecord->type] ?? 0xFF;
                $answer->class    = ['IN' => 1, 'CS' => 2, 'CH' => 3, 'HS' => 4][$dnsrecord->class] ?? 0xFF;
                $answer->ttl      = max(300, $dnsrecord->ttl); // use min 300
                $answer->dataRaw  = hex2bin($dnsrecord->ip);
                $answers[]        = $answer;
            }
        }

        if(count($answers) === 0) {
            echo "│   └── fetch records from 3rd party DNS Server\n";

            if($request->arCount) {
                $request->setAdditional([]);
//                echo "│   └── nah! forget about it. it has some Additional RRs. I have no idea what I should do with it\n";
//                return $answers;
            }

            try {
                // delete outdated entries
                // TODO move to scheduler
                app('db')->insert('DELETE from dnszones WHERE DATETIME(updated_at, \'+\' || ttl || \' seconds\') < CURRENT_TIMESTAMP');
            } catch (\Throwable $t) {
                Log::error($t);
            }

            // fetch records from 3rd party DNS Server
            $dnsClient = new Client();
            $results = $dnsClient->askForRaw('1.1.1.1', $request->toRaw());
            // cache records
            foreach($results as $result) {
                /** @var Resource $result */
                try {
                    app('db')->insert('INSERT INTO dnszones (host, ip, class, type, ttl, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)', [
                        $result->domain,
                        bin2hex($result->dataRaw),
                        $result->getClassStr(),
                        $result->getTypeStr(),
                        $result->ttl,
                        Carbon::now()->format('Y-m-d H:i:s'),
                        Carbon::now()->format('Y-m-d H:i:s'),
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
