<?php

namespace Mahmoudigithub\DatabaseRelationTester\Traits;

use Illuminate\Database\Eloquent\Model;

trait MorphToRelationTesting {
    use AutoFindMorphToRelationMethodName;

    public function test_morph_to_relations()
    {
        // get in relation model|models
        $relationModel = $this->inMorphToRelationModel();

        // use multiple in relation model test when inRelationModel returns array of model
        if(is_array($relationModel)){
            $this->morphToMultipleTest($relationModel);
        }
        // use single in relation model test when inRelationModel returns just one model
        else{
            $this->morphToSingleTest($relationModel);
        }
    }

    // when inRelationModel returns array of models
    private function morphToMultipleTest(array $inRelationModels)
    {
        foreach($inRelationModels as $relationModel)
        {
            $relationMethod = $this->extract_relation_method_name($this->model() , $relationModel);
            $this->morphToExecuteTest($relationModel , $relationMethod);
        }
    }

    // when inRelationModel returns single model
    private function morphToSingleTest(Model $inRelationModel)
    {
        $relationMethod = $this->extract_relation_method_name($this->model() , $inRelationModel);
        $this->morphToExecuteTest($inRelationModel , $relationMethod);
    }



    private function morphToExecuteTest(Model $inRelationModel ,string $relationMethod)
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
    protected function inMorphToRelationModelMethodNames() : array|null
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


    abstract protected function inMorphToRelationModel():Model|array;





}
