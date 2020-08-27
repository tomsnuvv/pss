<?php

namespace App\Console\Commands\Executors\Info;

use App\Console\Commands\Contracts\ExecutorCommand;
use App\Models\Product;
use App\Libs\Executors\Info\Products as Executor;
use Illuminate\Support\Facades\DB;

/**
 * Products Info Executor command.
 *
 * Executes all the available info modules for products.
 */
class Products extends ExecutorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'info:products {id?}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Run all the info modules for products';

    /**
     * {@inheritdoc}
     */
    protected $no_items_message = 'No products found.';

    /**
     * {@inheritdoc}
     */
    protected $model = Product::class;

    /**
     * {@inheritdoc}
     */
    protected $executor = Executor::class;

    /**
     * {@inheritdoc}
     */
    protected $field = 'code';

    /**
     * {@inheritdoc}
     */
    protected $title = 'Gathering Product information';

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        // Disable the query log, as it's a long process.
        DB::connection()->disableQueryLog();

        parent::__construct();
    }
}
