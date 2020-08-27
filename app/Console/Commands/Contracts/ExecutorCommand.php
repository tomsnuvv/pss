<?php

namespace App\Console\Commands\Contracts;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

abstract class ExecutorCommand extends Command
{
    /**
     * The item model class.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The executor class.
     *
     * @var \App\Libs\Contracts\Executors\Abstracts\Executor
     */
    protected $executor;

    /**
     * The item field to display.
     *
     * @var string
     */
    protected $field;

    /**
     * Title to display.
     *
     * @var string
     */
    protected $title;

    /**
     * No items display message.
     *
     * @var string
     */
    protected $no_items_message;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get items
        $items = $this->getItems();

        if (!count($items)) {
            $this->comment($this->no_items_message);

            return;
        }

        foreach ($items as $item) {
            $this->action($item);
        }
    }

    /**
     * Query all the items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function queryAll()
    {
        return $this->model::inRandomOrder();
    }

    /**
     * Method to get the items.
     *
     * @return array
     */
    protected function getItems()
    {
        $id = $this->argument('id');
        if (is_numeric($id)) {
            return $this->model::whereId($id)->get();
        } elseif ($id === 'single') {
            return $this->queryAll()->limit(1)->get();
        }

        return $this->queryAll()->get();
    }

    /**
     * Action to perform for each item.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    protected function action(Model $model)
    {
        $this->showItem($model);
        $executor = new $this->executor($model, $this->output);
        $executor->run();
    }

    /**
     * Show the item output.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    protected function showItem(Model $model)
    {
        $field = $this->field;
        $this->line(PHP_EOL . '<info> [+] ' . $this->title . ': </info><comment>' . $model->$field . '</comment>');
        $this->line('');
    }
}
