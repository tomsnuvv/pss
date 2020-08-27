<?php

use Illuminate\Database\Seeder;
use App\Models\Module;

class ModulesSeeder extends Seeder
{
    /**
     * All Modules
     *
     * @var array
     */
    const MODULES = [
        /**
         * Import Modules
         */
        'Import\Repositories\Github',
        'Import\Vulnerabilities\Composer\SecurityAdvisories',
        'Import\Vulnerabilities\WordPress\WPVulnDB',
        'Import\Vulnerabilities\Javascript\Nodejs\SecurityWG',
        'Import\Vulnerabilities\NIST\NVD',

        /**
         * Info Modules
         */
        'Info\Products\Composer\Packagist',
        'Info\Products\Javascript\Npmjs',
        'Info\Products\Javascript\Yarnpkg',
        'Info\Products\WordPress\WPAPI',
        'Info\Products\Jenkins\Plugins\Jenkins',

        /**
         * Discovery Modules
         */
        'Discovery\Websites\Headers',
        'Discovery\Websites\Domains',
        'Discovery\Websites\Host',
        'Discovery\Websites\Status',
        'Discovery\Websites\Certificate',
        'Discovery\Websites\Browsershot',
        'Discovery\Websites\Products\WordPress\WPPSS',
        'Discovery\Websites\Products\WordPress\WPScan',
        'Discovery\Websites\Products\Jenkins\JPSS',
        'Discovery\Websites\Products\WhatWeb',
        'Discovery\Websites\Requests\CommonCrawl',
        'Discovery\Websites\Requests\Otxurls',
        'Discovery\Websites\Requests\Archive',
        'Discovery\Websites\Requests\Crawler',
        'Discovery\Websites\Requests\Contents',
        'Discovery\Domains\NameServers',
        'Discovery\Domains\DNS\DNSRecon',
        'Discovery\Domains\Whois',
        'Discovery\Domains\Subdomains\Sonar',
        'Discovery\Domains\Subdomains\Amass',
        'Discovery\Domains\Subdomains\OneForAll',
        'Discovery\Domains\Subdomains\MassDNS',
        'Discovery\Domains\Website',
        'Discovery\Domains\Host',
        'Discovery\Domains\Requests\AlienVaultOTX',
        'Discovery\Repositories\Products\Composer',
        'Discovery\Repositories\Products\Yarn',
        'Discovery\Hosts\Products\PSS',
        'Discovery\Hosts\Ports\Nmap',
        'Discovery\Hosts\Ports\Masscan',
        'Discovery\Hosts\Domains\ReverseIPuk',
        'Discovery\Ports\Websites',
        'Discovery\Ports\Installations\Nmap',
        'Discovery\Organisations\Hosts\Censys',
        'Discovery\Organisations\Hosts\Shodan',
        'Discovery\Organisations\Websites\Shodan',
        'Discovery\Certificates\Domain',

        /**
         * Audit Modules
         */
        'Audit\Websites\HTTP',
        'Audit\Websites\WAF',
        'Audit\Websites\Headers',
        'Audit\Websites\HostHeaderInjection',
        'Audit\Websites\HostHeaderIPLeak',
        'Audit\Websites\Requests\GoogleMaps',
        'Audit\Websites\Requests\MixedContent',
        'Audit\Websites\VersionControlSystems',
        'Audit\Websites\Products\WordPress\XMLRPC',
        'Audit\Websites\Products\WordPress\Login\Unrestricted',
        'Audit\Websites\Products\WordPress\Enumeration\Author',
        'Audit\Websites\Products\WordPress\Enumeration\Feed',
        'Audit\Websites\Products\WordPress\Enumeration\Login',
        'Audit\Websites\Products\WordPress\Enumeration\WPAPI',
        'Audit\Installations\Vulnerabilities',
        'Audit\Installations\Outdated',
        'Audit\Domains\Whois\Expiration',
        'Audit\Domains\Certificate\Expiration',
        'Audit\Domains\Certificate\TestSSL',
        'Audit\Domains\Takeover\Subjack',
        'Audit\Domains\DNS\ZoneTransfer',
        'Audit\Domains\Email\DMARC',
        'Audit\Repositories\Secrets\TruffleHog',
        'Audit\Hosts\Ports\Bruteforce\Hydra',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::MODULES as $module) {
            Module::firstOrCreate([
                'code' => $module
            ]);
        }
    }
}
