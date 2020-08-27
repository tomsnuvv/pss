<?php

namespace App\Libs\Modules\Audit\Repositories\Secrets;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use \App\Libs\Contracts\Modules\Traits\Process;
use Illuminate\Support\Facades\Storage;
use App\Libs\Providers\Repositories\Git;
use App\Models\Severity;

/**
 * TruggleHog Audit Module.
 *
 * https://github.com/dxa4481/truffleHog
 */
class TruffleHog extends Audit
{
    use Process;

    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'LEAKED_CREDENTIALS';

    /**
     * Path to the temporary output file.
     *
     * @var string
     */
    protected $tmp;

    /**
     * Repository provider.
     *
     * @var \App\Libs\Providers\Repositories\Git
     */
    protected $provider;

    /**
     * Results from TruggleHog output
     *
     * @var array
     */
    protected $results = [];

    /*
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->provider = new Git($this->model);
        $this->tmp = 'outputs/trufflehog_' . $this->model->id . '.json';
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // trufflehog -x storage/app/trufflehog_exclude.txt --regex --rules storage/app/trufflehog_rules.json --entropy=False ../colruyt --json > storage/app/trufflehog_output.json
        if ($this->environment != 'local' || !Storage::exists($this->tmp)) {
            $output = $this->runProcess([
                env('TOOLS_TRUFFLEHOG'),
                '--regex', '--entropy', 'False', '--json',
                '--exclude_paths', storage_path('app/settings/trufflehog/exclude.txt'),
                '--rules', storage_path('app/settings/trufflehog/rules.json'),
                $this->provider->getLocalRepositoryPath(),
            ], true);
            Storage::put($this->tmp, $output);
        }
        $output = Storage::get($this->tmp);
        if ($this->environment != 'local') {
            Storage::delete($this->tmp);
        }
        // No results
        if (!$output) {
            return;
        }
        $this->process($output);
        $this->store();
    }

    /**
     * Process the TruffleHog json output.
     *
     * @param  string $output
     */
    private function process($output)
    {
        // Fix non-standar JSON
        $output = '[' . $output . ']';
        $output = str_replace("}\n{", '},{', $output);

        $json = json_decode($output);
        if (empty($json)) {
            throw new \Exception('Output JSON malformed');
        }

        foreach ($json as $item) {
            foreach ($item->stringsFound as $string) {
                $this->results[$string][] = $item;
            }
        }
    }

    /**
     * Store the findings
     */
    private function store()
    {
        foreach ($this->results as $string => $results) {
            // False positives
            if (strlen($string) > 200) {
                continue;
            }
            $reason = $results[0]->reason;
            $details  = '**String found:** ' . $string . PHP_EOL . PHP_EOL;
            $details .= '**Type:** ' . $reason . PHP_EOL . PHP_EOL;
            $details .= '**Found in**: ' . PHP_EOL;
            foreach ($results as $i => $result) {
                if ($i > 5) {
                    $details .= '(results truncated, total commits found: ' . count($results) . ')';
                    break;
                }
                $details .= '```' . PHP_EOL;
                $details .= 'Branch: ' . $result->branch . PHP_EOL;
                $details .= 'Commit: ' . str_replace(array("\n", "\r"), '', $result->commit) . PHP_EOL;
                $details .= 'Hash: ' . $result->commitHash . PHP_EOL;
                $details .= 'Date: ' . $result->date . PHP_EOL;
                $details .= 'Path: ' . $result->path . PHP_EOL;
                $details .= 'Link: ' . $this->model->url . '/blob/' . $result->commitHash . '/' . $result->path . PHP_EOL;
                $details .= '```' . PHP_EOL;
            }

            $type = $this->getVulnerabilityType();
            $title = $type->name . ' - ' . $reason;
            $uid = crc32($string);

            $finding = $this->storeFinding(null, null, null, null, $details, $title, $uid);

            if ($this->model->isPublic()) {
                $finding->severity()->associate(Severity::where('name', 'Critical')->first());
                $finding->save();
            }

            $this->items[] = $finding;
        }
    }
}
