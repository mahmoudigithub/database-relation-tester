<?php

namespace Mahmoudigithub\DatabaseRelationTester\Traits;

use Illuminate\Database\Eloquent\Model;

trait InsertQueryTesting {
    public function test_insert_new_row_to_model_table(): void
    {
        // get current model
        $model = $this->model();

        // get current model table name
        $table = $model->getTable();

        // get new instance of current model data from its factory maker
        $newCurrentModelData = $model::factory()->make()->toArray();

        // create new current model in database by by newCategoryData variable
        $model::create($newCurrentModelData);

        // assert that fake data not empty
        $this->assertTrue(!empty($newCurrentModelData) && $newCurrentModelData);

        // assert that new current model row exists in database related table
        $this->assertDatabaseHas($table , $newCurrentModelData);
    }

    abstract protected function model() : Model;
}
