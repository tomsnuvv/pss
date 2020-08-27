# Setup

## GUI

### Laravel Nova

For the GUI, this tool uses Laravel Nova (commercial), which requires a license.

Setup [Laravel Nova credentials](https://nova.laravel.com/docs/3.0/installation.html#authenticating-nova-in-continuous-integration-ci-environments):

```
composer config http-basic.nova.laravel.com <user> <password>
```
Or manually create the [auth.json](https://getcomposer.org/doc/articles/http-basic-authentication.md) file.

### No GUI

The tool can be used without GUI. Just remove the Laravel Nova dependency from the composer and proceed with the rest of the installation.

## Docker

```
docker-compose build
```
```
docker-compose start
```

## Non-docker

There is an automated install script in `docker/install.sh` that "might be useful". It was build for Debian based OS.

### Requirements
Software requirements:
* `php >=7.2.5`
* `mysql >=5.7.7`
* `nmap`
* `git`
* `nodejs`
* `npm`
* [amass](https://github.com/caffix/amass/releases)
* [whatweb](https://github.com/urbanadventurer/WhatWeb/)
* [testssl.sh](https://github.com/drwetter/testssl.sh)
* [Subjack](https://github.com/haccer/subjack) (Requires GO)
* [DNSRecon](https://github.com/darkoperator/dnsrecon)
* [Checkdmarc](https://github.com/domainaware/checkdmarc)
* [masscan](https://github.com/robertdavidgraham/masscan)
* [wpscan](https://github.com/wpscanteam/wpscan)
* [cc.py](https://github.com/si9int/cc.py)
* [oneforall](https://github.com/shmilylty/OneForAll)
* [massdns](https://github.com/blechschmidt/massdns)
* [trufflehog](https://github.com/dxa4481/truffleHog)

Make sure to add GOPATH env (`export GOPATH=$HOME/go`).
https://github.com/haccer/subjack/issues/25

`masscan` and `oneforall` need to run as root. Make sure php user is able to run those with sudo with `NOPASSWD` directive.

### Setup

### PHP Dependencies
```
composer install
```

### Environment variables

#### Modules
* `TOOLS_AMASS`: Amass path.
* `TOOLS_WHATWEB`: WhatWeb path.
* `TOOLS_TESTSSL`: Testssl.sh path.
* `TOOLS_SUBJACK`: Subjack path.
* `TOOLS_SUBJACK_CONFIG`: Subjack fingerprints.json path (optional).
* `TOOLS_DNSRECON`: DNSRecon path.
* `TOOLS_CHECKDMARC`: Checkdmarc path.
* `TOOLS_MASSCAN`: Masscan path.
* `TOOLS_WPSCAN`: WPScan path.
* `TOOLS_NODE`: NodeJS path.
* `TOOLS_NPM`: NPM path.
* `TOOLS_CC`: cc.py path.
* `TOOLS_ONEFORALL`: OneForAll path.
* `TOOLS_CRAWLER`: Crawler script path (/app/bin/crawler.js).
* `TOOLS_CONTENTS`: Contents script path (/app/bin/contents.js).
* `TOOLS_MASSDNS`: MassDNS binary path.
* `TOOLS_MASSDNS_WORDLIST`: MassDNS subdomain list.
* `TOOLS_MASSDNS_RESOLVERS`: MassDNS resolvers list.
* `TOOLS_TRUFFLEHOG`: trufflehog path.
* `TOOLS_SONAR_BIN`: Rapid7 Sonar script path (/app/bin/sonar.sh).
* `TOOLS_SONAR_DATA`: Downloaded data from Rapid7 (wget -O fdns_cname.json.gz https://opendata.rapid7.com/sonar.fdns_v2/2020-04-24-1587686803-fdns_cname.json.gz).

#### API Keys & Tokens
* `CENSYS_CREDENTIALS`: Censys API. For Organisation searches. (<API_ID>:<Secret>)
* `SHODAN_TOKEN`: Shodan API. For Organisation searches.
* `REVERSE_IP_UK_API`: Reverse-ip.uk API. For reverse IP searches.
* `WPVULNDB_TOKEN`: WordPress vulnerabilities.

#### Auth
* `AUTH_DOMAIN`: Authorised domain for SSO.
* `SOCIALITE_GOOGLE_CLIENT_ID`: Google SSO integration.
* `SOCIALITE_GOOGLE_CLIENT_SECRET`: Google SSO integration.


### Integrations

Integrations allows the tool to configure and use services. All integrations are optional. The settings field stores json content.

#### Github
Required for Github searches and imports. Will import all repositories from the defined organisations. Fields:
* `Token`: The Github Token.
* `Settings`:
```json
{
    "orgs": [
        "Starbucks",
        "uber"
    ]
}
```

#### Slack
Required for Slack notifications. Will notify on new Findings. Fields:
* `Token`: The Slack webhook url.
* `Settings`:
```json
{
    "min_severity": 4
}
```
`min_severity` is the ID of the minimum severity to be notified. If that's not defined, will notify all new findings.

### Cron & daemon

For executing the tasks, the [Laravel task scheduling](https://laravel.com/docs/5.2/scheduling#introduction) cronjob is required:
```
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

And also, for executing the GUI tasks, the [Laravel queue listener](https://laravel.com/docs/5.2/queues#daemon-queue-listener):
```
php /path/to/artisan queue:listen --tries=3 --timeout=1800
```