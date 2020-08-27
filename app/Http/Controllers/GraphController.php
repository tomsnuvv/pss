<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;

set_time_limit(0);

class GraphController extends Controller
{
    /**
     * Graph data.
     *
     * @var array
     */
    private $data = [
        'nodes' => [],
        'edges' => [],
    ];

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function index(Project $project)
    {
        foreach ($project->hosts as $host) {
            $this->addHost($host);
        }

        foreach ($project->domains as $domain) {
            $this->addDomain($domain);
        }

        foreach ($project->websites()->where('key', 1)->get() as $website) {
            $this->addNode('web', $website->id, $website->url, $website->getMaxSeverity());
        }

        foreach ($project->repositories as $repository) {
            $this->addNode('repo', $repository->id, $repository->name, $repository->getMaxSeverity());
        }

        return view('graph')->with(['data' => $this->data]);
    }

    /**
     * Add a host.
     *
     * @param \App\Models\Host $host
     */
    private function addHost($host)
    {
        if (!$host->key) {
            return;
        }

        if ($host->name) {
            $hostText = $host->name;
        } else {
            $hostText = $host->ip;
        }

        $this->addNode('host', $host->id, $hostText, $host->getMaxSeverity());

        foreach ($host->ports as $port) {
            $portText = $port->port;
            if ($port->service) {
                $portText .= ' (' . $port->service . ')';
            }
            $this->addNode('port', $port->id, $portText);
            $this->addEdge('host_' . $host->id, 'port_' . $port->id);
        }
        foreach ($host->websites()->pluck('websites.id') as $websiteId) {
            $this->addEdge('host_' . $host->id, 'web_' . $websiteId);
        }
        foreach ($host->certificates()->pluck('certificates.id') as $certificateId) {
            $this->addEdge('host_' . $host->id, 'cert_' . $certificateId);
        }
    }

    /**
     * Add a subdomain (and its subdomains & parents).
     *
     * @param \App\Models\Domain $domain
     * @param string $nodeId
     */
    private function addDomain($domain, $nodeId = null)
    {
        if (!$domain->key) {
            return;
        }
        $this->addNode('domain', $domain->id, $domain->name, $domain->getMaxSeverity());
        if ($nodeId) {
            $this->addEdge($nodeId, 'domain_' . $domain->id);
        }

        foreach ($domain->websites()->pluck('websites.id') as $websiteId) {
            $this->addEdge('domain_' . $domain->id, 'web_' . $websiteId);
        }

        if ($domain->host_id) {
            $this->addHost($domain->host);
            $this->addEdge('domain_' . $domain->id, 'host_' . $domain->host->id);
        }

        if ($domain->certificate_id) {
            $this->addNode('cert', $domain->certificate->id, $domain->certificate->subject_common_name);
            $this->addEdge('domain_' . $domain->id, 'cert_' . $domain->certificate->id);
        }

        if ($domain->domain_id) {
            $this->addDomain($domain->parent, 'domain_' . $domain->id);
        }

        if ($domain->dns_id) {
            foreach ($domain->dns as $dns) {
                $this->addNode('dns', $dns->id, $dns->type);
                $this->addEdge('domain_' . $domain->id, 'dns_' . $dns->id);
                if ($dns->target) {
                    if (get_class($dns->target) == 'App\\Models\\Domain') {
                        if ($dns->target->id != $domain->id) {
                            $this->addDomain($dns->target);
                        }
                        $this->addEdge('dns_' . $dns->id, 'domain_' . $dns->target->id);
                    } elseif (get_class($dns->target) == 'App\\Models\\Host') {
                        $this->addHost($dns->target);
                        $this->addEdge('host_' . $dns->target->id, 'dns_' . $dns->id);
                    }
                }
            }
        }
    }

    /**
     * Adds a node into data array.
     *
     * @param string $group
     * @param int    $id
     * @param string $label
     * @param bool   $severity
     */
    private function addNode($group, $id, $label, $severity = null)
    {
        $color = '#32cd32';
        if ($severity) {
            switch(strtolower($severity)){
                case 'info':
                    $color = '#87ceeb';
                break;
                case 'low':
                    $color = '#ffd700';
                break;
                case 'medium':
                    $color = '#ffa500';
                break;
                case 'high':
                    $color = '#ff6347';
                break;
                case 'critical':
                    $color = '#f00';
                break;
            }
        }

        $this->data['nodes'][$group . '_' . $id] = [
            'label' => $label,
            'group' => $group,
            'color' => $color,
        ];
    }

    /**
     * Adds a node into data array.
     *
     * @param string $from
     * @param string $to
     */
    private function addEdge($from, $to)
    {
        $this->data['edges'][$from . '-' . $to] = [
            'from' => $from,
            'to'   => $to,
        ];
    }
}
