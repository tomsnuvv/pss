<?php

namespace App\Console\Commands\Tools;

use App\Libs\Helpers\Domains;
use App\Libs\Helpers\Websites;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Migration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the Websites and Tokens from the old PSS';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $results = $this->getOldDatabaseResults();
        foreach ($results as $result) {
            $this->line('Importing <info> '.$result->url.'</info>');
            $website = Websites::createWebsite($result->url);
            $this->setKey($website);

            if (!$this->verifyWebsites($result->url, $website->url)) {
                continue;
            }

            if (!$website->token) {
                $website->token()->create(['token' => $result->token]);
                $this->line('Token created: <comment> '.$result->token.'</comment>');
            } elseif ($website->token->token != $result->token) {
                $this->line('Tokens are different: <comment> '.$website->token->token.'</comment> != <comment>'.$result->token.'</comment>');
                $token = $website->token;
                $token->fill(['token' => $result->token]);
                $token->save();
                $this->line('Token updated: <comment> '.$result->token.'</comment>');
            } else {
                $this->line('Token already exists');
            }
        }
    }

    /**
     * Get the old database websites & tokens.
     *
     * @return mixed
     */
    private function getOldDatabaseResults()
    {
        return DB::connection('mysql_old_pss')
            ->table('websites')
            ->leftJoin('tokens', function ($join) {
                $join->on('websites.id', '=', 'tokens.model_id');
                $join->on('tokens.model_type', '=', DB::raw('"App\\\Models\\\Website"'));
            })
            ->select('websites.url', 'tokens.token')
            ->get();
    }

    /**
     * Check if the old website and the new website's match.
     *
     * @param  string  $oldUrl
     * @param  string  $newUrl
     * @return bool
     */
    private function verifyWebsites($oldUrl, $newUrl)
    {
        $oldDomain = Domains::getDomainFromURL($oldUrl);
        $newDomain = Domains::getDomainFromURL($newUrl);

        if ($oldDomain != $newDomain) {
            $this->line('Websites mismatch: <comment> '.$oldDomain.'</comment> != <comment>'.$newDomain.'</comment>');
            $this->error('Token ignored!');
            return false;
        }

        return true;
    }

    /**
     * Set the website (and domain / host) as a key item.
     *
     * @param  \App\Models\Website  $website
     */
    private function setKey($website)
    {
        $website->key = true;
        $website->save();
        foreach ($website->domains()->get() as $domain) {
            $domain->key = true;
            $domain->save();
        }
        foreach ($website->hosts()->get() as $host) {
            $host->key = true;
            $host->save();
        }
    }
}
