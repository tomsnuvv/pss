<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Certificate'          => 'App\Policies\CertificatesPolicy',
        'App\Models\DNS'                  => 'App\Policies\DNSPolicy',
        'App\Models\Domain'               => 'App\Policies\DomainsPolicy',
        'App\Models\Finding'              => 'App\Policies\FindingsPolicy',
        'App\Models\FindingStatus'        => 'App\Policies\FindingStatusesPolicy',
        'App\Models\Header'               => 'App\Policies\HeadersPolicy',
        'App\Models\Host'                 => 'App\Policies\HostsPolicy',
        'App\Models\HostType'             => 'App\Policies\HostTypesPolicy',
        'App\Models\Installation'         => 'App\Policies\InstallationsPolicy',
        'App\Models\Integration'          => 'App\Policies\IntegrationsPolicy',
        'App\Models\ModuleLog'            => 'App\Policies\ModuleLogsPolicy',
        'App\Models\Module'               => 'App\Policies\ModulesPolicy',
        'App\Models\Nameserver'           => 'App\Policies\NameserversPolicy',
        'App\Models\Organisation'         => 'App\Policies\OrganisationsPolicy',
        'App\Models\Port'                 => 'App\Policies\PortsPolicy',
        'App\Models\Product'              => 'App\Policies\ProductsPolicy',
        'App\Models\ProductType'          => 'App\Policies\ProductTypesPolicy',
        'App\Models\ProductLicense'       => 'App\Policies\ProductLicensesPolicy',
        'App\Models\Project'              => 'App\Policies\ProjectsPolicy',
        'App\Models\Repository'           => 'App\Policies\RepositoriesPolicy',
        'App\Models\Role'                 => 'App\Policies\RolesPolicy',
        'App\Models\Severity'             => 'App\Policies\SeveritiesPolicy',
        'App\Models\Tokens'               => 'App\Policies\TokensPolicy',
        'App\Models\User'                 => 'App\Policies\UsersPolicy',
        'App\Models\Vulnerability'        => 'App\Policies\VulnerabilitiesPolicy',
        'App\Models\VulnerabilityDetail'  => 'App\Policies\VulnerabilityDetailsPolicy',
        'App\Models\VulnerabilityType'    => 'App\Policies\VulnerabilityTypesPolicy',
        'App\Models\VulnerabilityVersion' => 'App\Policies\VulnerabilityVersionsPolicy',
        'App\Models\Website'              => 'App\Policies\WebsitesPolicy',
        'App\Models\Whois'                => 'App\Policies\WhoisPolicy',

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
