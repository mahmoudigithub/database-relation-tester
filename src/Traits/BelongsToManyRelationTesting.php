<?php

namespace Mahmoudigithub\DatabaseRelationTester\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;

trait BelongsToManyRelationTesting
{
    use AutoFindRelationMethodName;

    public function test_Belongs_To_Many_Model_Relations()
    {
        // get in relation model|models
        $relationModel = $this->inBelongsToManyRelationModel();

        // use multiple in relation model test when inRelationModel returns array of model
        if(is_array($relationModel)){
            $this->belongsToManyMultipleTest($relationModel);
        }
        // use single in relation model test when inRelationModel returns array of model
        else{
            $this->belongsToManySingleTest($relationModel);
        }
    }

    // when inRelationModel returns array of models
    private function belongsToManyMultipleTest(array $inRelationModels)
    {
        foreach($inRelationModels as $relationModel)
        {
            $relationMethod = $this->extract_relation_method_name($this->model() , $relationModel);
            $this->belongsToManyExecuteTest($relationModel , $relationMethod);
        }
    }

    // when inRelationModel returns single model
    private function belongsToManySingleTest(Model $inRelationModel)
    {
        $relationMethod = $this->extract_relation_method_name($this->model() , $inRelationModel);
        $this->belongsToManyExecuteTest($inRelationModel , $relationMethod);
    }



    private function belongsToManyExecuteTest(Model $inRelationModel ,string $relationMethod)
    {
        // count of new relationModel instance that will create for currentModel
        $count = rand(1 , 9);

        // get currentModel
        $currentModel = $this->model();

        // get currentModel table
        $inRelationModelTableName = $inRelationModel->getTable();

         // create new instance of current model in database by a belongs instance of in relation model
        $currentModelInstance = $currentModel::factory()->hasAttached($inRelationModel::factory($count))->create();

        // assert return type of relation method
        $this->assertTrue($currentModelInstance->$relationMethod[0] instanceof $inRelationModel);

        // assert id of card`s person isset
        $this->assertTrue($currentModelInstance->$relationMethod[0]->id != null);

        // assert person exists in database
        $this->assertDatabaseHas($inRelationModelTableName , $currentModelInstance->$relationMethod[0]->withoutRelations()->toArray());

    }

    abstract protected function model():Model;

    // this method can be over written for define relation method name else trait try to find it automatic and if can not return error exception
    protected function inbelongsToManyRelationModelMethodNames() : array|null
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


    abstract protected function inbelongsToManyRelationModel():Model|array;
}
