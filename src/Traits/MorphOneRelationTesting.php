<?php

namespace Mahmoudigithub\DatabaseRelationTester\Traits;

use Illuminate\Database\Eloquent\Model;

trait MorphOneRelationTesting
{
    use AutoFindRelationMethodName;

    public function test_Morph_One_Model_Relations()
    {
        // get in relation model|models
        $relationModel = $this->inMorphOneRelationModel();

        // use multiple in relation model test when inRelationModel returns array of model
        if(is_array($relationModel)){
            $this->morphOneMultipleTest($relationModel);
        }
        // use single in relation model test when inRelationModel returns array of model
        else{
            $this->morphOneSingleTest($relationModel);
        }
    }

    // when inRelationModel returns array of models
    private function morphOneMultipleTest(array $inRelationModels)
    {
        foreach($inRelationModels as $relationModel)
        {
            $relationMethod = $this->extract_relation_method_name($this->model() , $relationModel);
            $this->morphOneExecuteTest($relationModel , $relationMethod);
        }
    }

    // when inRelationModel returns single model
    private function morphOneSingleTest(Model $inRelationModel)
    {
        $relationMethod = $this->extract_relation_method_name($this->model() , $inRelationModel);
        $this->morphOneExecuteTest($inRelationModel , $relationMethod);
    }



    private function morphOneExecuteTest(Model $InRelationModel ,string $relationMethod)
    {
        // get currentModel
        $currentModel = $this->model();

        // get currentModel table
        $inRelationModelTableName = $InRelationModel->getTable();

         // create new instance of current model in database by a belongs instance of in relation model
        $currentModelInstance = $currentModel::factory()->has($InRelationModel::factory() , $relationMethod)->create();

        // assert relation method return a in relation model instance
        $this->assertTrue($currentModelInstance->$relationMethod instanceof $InRelationModel);

        // assert relation address id is set
        $this->assertTrue(isset($currentModelInstance->$relationMethod->id));

        // assert user address exists in database
        $this->assertDatabaseHas($inRelationModelTableName , $currentModelInstance->$relationMethod->toArray());
    }

    abstract protected function model():Model;

    // this method can be over written for define relation method name else trait try to find it automatic and if can not return error exception
    protected function inMorphOneRelationModelMethodNames() : array|null
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


    abstract protected function inMorphOneRelationModel():Model|array;
}
