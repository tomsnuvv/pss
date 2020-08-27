# Modules

Modules are Jobs, that are executed periodically, in order to audit (or be able to audit) the different items (hosts, websites, repositories...).

Modules will ONLY be executed on items that are set to be `key`.

There are different type of modules, depending of it's functionality:
* `Discovery:` Basically perform discovery / recon tasks on the target models, such as Websites, Hosts, Domains, Repositories...
* `Import:` Feed the database with a list of resources, like published Vulnerabilities, Reports or organisation Repositories.
* `Info:` Get information about a certain model, so far only Products. For example, when a new Product is introduced, will grab (from different sources) the latest available version and some other useful information.
* `Audit:` Are the modules that perform the real audit on the gathered information. Those modules will generate Findings (or update them if fixed).

## List of modules

|Type     |Code                                        |From                 |Spawns                        |Description |
|---------|--------------------------------------------|---------------------|------------------------------|--------------------------------------------------------------------------------|
|Discovery|Organisations\Hosts\Censys                  |Organisation         |Hosts                         |From an Organisation name, obtains Hosts using Censys.io API |
|Discovery|Organisations\Domains\Censys                |Organisation         |Domains                       |From an Organisation name, obtains Domains using Censys.io API |
|Discovery|Organisations\Hosts\Shodan                  |Organisation         |Hosts                         |From an Organisation name, obtains Hosts using Shodan.io API |
|Discovery|Domains\DNS\DNSrecon                        |Domains              |DNS Nameservers               |Obtains DNS records from a domain. It also generates Domains / Hosts related to the discovered DNS entries |
|Discovery|Domains\Requests\AlienVaultOTX              |Domains              |Requests                      |Obtains requests from a domain by using AlienVault Open Threat Exchange  |
|Discovery|Domains\Subdomains\Amass                    |Domains              |Domains                       |Enumerates subdomains from a top level domain using https://github.com/OWASP/Amass |
|Discovery|Domains\Subdomains\OneForAll                |Domains              |Domains                       |Enumerates subdomains from a top level domain using https://github.com/shmilylty/OneForAll/ |
|Discovery|Domains\Subdomains\Sonar                    |Domains              |Domains                       |Enumerates subdomains from a top level domain using https://opendata.rapid7.com/ |
|Discovery|Domains\Subdomains\MassDNS                  |Domains              |Domains                       |Enumerates subdomains from a top level domain using https://github.com/blechschmidt/massdns https://gist.github.com/jhaddix/f64c97d0863a78454e44c2f7119c2a6a |
|Discovery|Websites\Certificate                        |Websites             |Certificate Domain Host       |Obtains the Certificate information from a Website |
|Discovery|Domains\Whois                               |Domains              |Whois                         |Requests Whois information of the domain |
|Discovery|Domains\Host                                |Domains              |Host                          |From a Domain, resolves a Host |
|Discovery|Domains\Website                             |Domains              |Website                       |Does an HTTP request to the domain, to check if there is a website |
|Discovery|Repositories\Products\Composer              |Repository           |Products                      |Gathers composer packages from a composer.lock files, from a repository |
|Discovery|Repositories\Products\Yarn                  |Repository           |Products                      |Gathers javascript packages from a yarn.lock files, from a repository |
|Discovery|Hosts\Ports\Nmap                            |Hosts                |Ports Installations Products  |Traditional nmap port discovery for a few common ports. It also gets the service information of the discovered ports |
|Discovery|Hosts\Ports\Masscan                         |Hosts                |Ports                         |Masscan module for all port discovery. It doesn't obtain the service information, so it requires Ports\Installations\Nmap module later |
|Discovery|Hosts\Domains\ReverseIPuk                   |Hosts                |Domains                       |Discovers Domains from a Host using Reverse IP: https://www.reverse-ip.uk/ |
|Discovery|Ports\Installations\Nmap                    |Ports                |Installations Products        |From a port (previously discovered) tries to get the service information (version / product) |
|Discovery|Ports\Websites                              |Ports                |Websites                      |From a http / https service, tries to get a website |
|Discovery|Websites\Status                             |Websites             |Websites                      |Checks the HTTP status of the website |
|Discovery|Websites\Products\WordPress\WPPSS           |Websites             |Installations Products        |Endouble internal plugin to fetch WordPress information from a website |
|Discovery|Websites\Products\WordPress\WPScan          |Websites             |Installations Products        |Does the same as WPPSS, but with https://github.com/wpscanteam/wpscan |
|Discovery|Websites\Headers                            |Websites             |Headers                       |Grabs websites's Headers and Cookies, in order to identify missing security configurations |
|Discovery|Websites\Requests\Crawler                   |Websites             |Requests                      |Crawl a website obtaining its pages and content. Those can be used later for obtaining other resources, such as emails, products (in dependency links), linked websites... |
|Discovery|Websites\Requests\CommonCrawl               |Websites             |Requests                      |Obtains requests from Common Crawl API (https://commoncrawl.org/the-data/) |
|Discovery|Websites\Requests\Archive                   |Websites             |Requests                      |Obtains requests from Archive.org |
|Discovery|Websites\Requests\Contents                  |Websites             |Requests                      |Perform the stored requests and stores the content and response code |
|Discovery|Websites\Browsershot                        |Websites             |                              |Takes an image snapshot from the main website url |
|Discovery|Websites\Hosts                              |Websites             |Hosts                         |Grabs the Website resolved Host |
|Discovery|Websites\Domains                            |Websites             |Domains                       |Grabs the Website resolved Domain / Subdomain |
|Discovery|Websites\Products\Jenkins\JPSS              |Websites             |Installations Products        |API to push / pull Jenkins version and its plugins |
|Discovery|Websites\Products\WhatWeb                   |Websites             |Installations                 |https://github.com/urbanadventurer/WhatWeb |
|Audit    |Domains\DNS\ZoneTransfer                    |Domains              |Findings                      |Tries to perform DNS Zone Transfer attack on a domain.  https://digi.ninja/projects/zonetransferme.php |
|Audit    |Domains\Certificates\TestSSL                |Domains Certificates |Findings                      |Audits SSL Vulnerabilities (HEARTBLEED, ROBOT, BREACH...) by using https://github.com/drwetter/testssl.sh |
|Audit    |Domains\Certificates\Expiration             |Domains Certificates |Findings                      |Checks SSL certificate expiration |
|Audit    |Domains\Email\DMARC                         |Domains              |Findings                      |Audits SPF, DMARC & DKIM DNS records https://github.com/domainaware/checkdmarc |
|Audit    |Websites\WAF                                |Websites             |Findings                      |Checks if the website is running a Web Application Firewall by requesting common URL payloads |
|Audit    |Websites\Headers\Missing                    |Websites Headers     |Findings                      |Checks for missing security headers |
|Audit    |Websites\HostHeaderInjection                |Websites             |Findings                      |Checks if the website is vulnerable to Host Header Injection attack |
|Audit    |Installations\Vulnerabilities               |Installations        |Findings                      |Matches Vulnerabilities in the Product Installations |
|Audit    |Installations\Updates                       |Installations        |Findings                      |Matches missing updates in the Product Installations |
|Audit    |Websites\WordPress\Enumeration\Author       |Websites             |Findings                      |Checks for WordPress Author Enumeration |
|Audit    |Websites\WordPress\Enumeration\Feed         |Websites             |Findings                      |Checks for WordPress Feed Enumeration |
|Audit    |Websites\WordPress\Enumeration\Login        |Websites             |Findings                      |Checks for WordPress Login Enumeration |
|Audit    |Websites\WordPress\Enumeration\WPAPI        |Websites             |Findings                      |Checks for WordPress WPAPI Enumeration |
|Audit    |Websites\WordPress\Login\Unrestricted       |Websites             |Findings                      |Checks if the Login form is restricted to certain IPs |
|Audit    |Websites\WordPress\Login\Bruteforce         |Websites             |Findings                      |Perform a basic brute-force against WordPress login form |
|Audit    |Websites\WordPress\XMLRPC                   |Websites             |Findings                      |Checks if the XMLRPC interface is enabled |
|Audit    |Hosts\Ports\Bruteforce\Hydra                |Ports                |Findings                      |Performs a basic brute-force against services using Hydra |
|Audit    |Domains\Whois\Expiration                    |Whois                |Findings                      |Checks if the Whois expiration date is anytime soon |
|Audit    |Domains\Takeover\Subjack                    |Domains              |Findings                      |Checks if domains can be takeover https://github.com/haccer/subjack |
|Audit    |Websites\HTTP                               |Websites             |Findings                      |Checks if a HTTPS website is also server with HTTP |
|Audit    |Websites\VersionControlSystems              |Websites             |Findings                      |Checks for exposed GIT / SVN directories |
|Audit    |Websites\Requests\GoogleMaps                |Websites             |Findings                      |Checks for exposed Google Maps API keys and checks if are limited |
|Import   |Repositories\GitHub                         |                     |Repositories                  |Grabs all our GitHub repositories from the organisations defined in the Github Integration |
|Import   |Vulnerabilities\WordPress\WPVulnDB          |                     |Vulnerabilities Products      |Harvests public vulnerabilities affecting all WordPress related Products (core, themes and plugins), from https://wpvulndb.com/ |
|Import   |Vulnerabilities\Composer\SecurityAdvisories |                     |Vulnerabilities Products      |Grabs public vulnerabilities for Composer packages, from https://github.com/FriendsOfPHP/security-advisories |
|Import   |Vulnerabilities\Javascript\Nodejs\SecurityWG|                     |Vulnerabilities Products      |https://github.com/nodejs/security-wg |
|Import   |Vulnerabilities\NVD                         |                     |Vulnerabilities Products      |https://nvd.nist.gov/  https://nvd.nist.gov/vuln/data-feeds#JSON_FEED |
|Info     |Products\WordPress\WPAPI                    |Products             |                              |Obtains the details from https://wordpress.org/plugins/plugin_name/. |
|Info     |Products\Composer\Packagist                 |Products             |                              |Grabs available Product information from Packagist |
|Info     |Products\Javascript\Yarnpkg                 |Products             |                              |Grabs available Product information from Yarnpkg |
|Info     |Products\Jenkins\Plugins\Jenkins            |Products             |                              |Grabs info from https://plugins.jenkins.io/ |
