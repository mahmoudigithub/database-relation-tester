<?php

namespace Mahmoudigithub\DatabaseRelationTester\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Mahmoudigithub\DatabaseRelationTester\Traits\AutoFindRelationMethodName;

trait BelongsToRelationTesting
{
    use AutoFindRelationMethodName;

    public function test_Belongs_To_Model_Relations()
    {
        // get in relation model|models
        $relationModel = $this->inBelongsToRelationModel();

        // use multiple in relation model test when inRelationModel returns array of model
        if(is_array($relationModel)){
            $this->belongsToMultipleTest($relationModel);
        }
        // use single in relation model test when inRelationModel returns array of model
        else{
            $this->belongsToSingleTest($relationModel);
        }
    }

    // when inRelationModel returns array of models
    private function belongsToMultipleTest(array $inRelationModels)
    {
        foreach($inRelationModels as $relationModel)
        {
            $relationMethod = $this->extract_relation_method_name($this->model() , $relationModel);
            $this->belongsToExecuteTest($relationModel , $relationMethod);
        }
    }

    // when inRelationModel returns single model
    private function belongsToSingleTest(Model $inRelationModel)
    {
        $relationMethod = $this->extract_relation_method_name($this->model() , $inRelationModel);
        $this->belongsToExecuteTest($inRelationModel , $relationMethod);
    }



    private function belongsToExecuteTest(Model $inRelationModel ,string $relationMethod)
    {
        // get currentModel
        $currentModel = $this->model();

        // get currentModel table
        $inRelationModelTableName = $inRelationModel->getTable();

         // create new instance of current model in database by a belongs instance of in relation model
        $currentModelInstance = $currentModel::factory()->for($inRelationModel::factory() , $relationMethod)->create();

        // assert return type of relation method
        $this->assertTrue($currentModelInstance->$relationMethod instanceof $inRelationModel);

        // assert id of card`s person isset
        $this->assertTrue($currentModelInstance->$relationMethod->id != null);

        // assert person exists in database
        $this->assertDatabaseHas($inRelationModelTableName , $currentModelInstance->$relationMethod->toArray());

    }

    abstract protected function model():Model;

    // this method can be over written for define relation method name else trait try to find it automatic and if can not return error exception
    protected function inBelongsToRelationModelMethodNames() : array|null
    {
        /*
            array templete for example:
                [
                    'address' => new Address()
                ]

                [
                    {relationMethod} => {relationModel}
                ]
        */
        return null;
    }


    abstract protected function inBelongsToRelationModel():Model|array;
}
