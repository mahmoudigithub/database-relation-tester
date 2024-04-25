<?php

namespace Mahmoudigithub\DatabaseRelationTester\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;

trait HasOneRelationTesting
{
    use AutoFindRelationMethodName;

    public function test_Has_One_Model_Relations()
    {
        // get in relation model|models
        $relationModel = $this->inHasOneRelationModel();

        // use multiple in relation model test when inRelationModel returns array of model
        if(is_array($relationModel)){
            $this->multipleTest($relationModel);
        }
        // use single in relation model test when inRelationModel returns array of model
        else{
            $this->singleTest($relationModel);
        }
    }

    // when inRelationModel returns array of models
    private function multipleTest(array $inRelationModels)
    {
        foreach($inRelationModels as $relationModel)
        {
            $relationMethod = $this->extract_relation_method_name($this->model() , $relationModel);
            $this->execute($relationModel , $relationMethod);
        }
    }

    // when inRelationModel returns single model
    private function singleTest(Model $inRelationModel)
    {
        $relationMethod = $this->extract_relation_method_name($this->model() , $inRelationModel);
        $this->execute($inRelationModel , $relationMethod);
    }



    private function execute(Model $InRelationModel ,string $relationMethod)
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
    protected function inHasOneRelationModelMethodNames() : array|null
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

    abstract protected function inHasOneRelationModel():Model|array;
}
