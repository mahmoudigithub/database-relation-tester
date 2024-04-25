<?php

namespace Mahmoudigithub\DatabaseRelationTester\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;

trait HasManyRelationTesting
{
    use AutoFindRelationMethodName;

    public function test_Has_Many_Model_Relations()
    {
        // get in relation model|models
        $relationModel = $this->inHasManyRelationModel();

        // use multiple in relation model test when inRelationModel returns array of model
        if(is_array($relationModel)){
            $this->hasManyMultipleTest($relationModel);
        }
        // use single in relation model test when inRelationModel returns array of model
        else{
            $this->hasManySingleTest($relationModel);
        }
    }

    // when inRelationModel returns array of models
    private function hasManyMultipleTest(array $inRelationModels)
    {
        foreach($inRelationModels as $relationModel)
        {
            $relationMethod = $this->extract_relation_method_name($this->model() , $relationModel);
            $this->hasManyExecuteTest($relationModel , $relationMethod);
        }
    }

    // when inRelationModel returns single model
    private function hasManySingleTest(Model $inRelationModel)
    {
        $relationMethod = $this->extract_relation_method_name($this->model() , $inRelationModel);
        $this->hasManyExecuteTest($inRelationModel , $relationMethod);
    }



    private function hasManyExecuteTest(Model $InRelationModel ,string $relationMethod)
    {
        // get currentModel
        $currentModel = $this->model();

        // get currentModel table
        $inRelationModelTableName = $InRelationModel->getTable();

         // create new instance of current model in database by a belongs instance of in relation model
        $currentModelInstance = $currentModel::factory()->has($InRelationModel::factory() , $relationMethod)->create();

        // assert relation method return a in relation model instance
        $this->assertTrue($currentModelInstance->$relationMethod[0] instanceof $InRelationModel);

        // assert relation address id is set
        $this->assertTrue(isset($currentModelInstance->$relationMethod[0]->id));

        // assert user address exists in database
        $this->assertDatabaseHas($inRelationModelTableName , $currentModelInstance->$relationMethod[0]->toArray());
    }

    abstract protected function model():Model;

    // this method can be over written for define relation method name else trait try to find it automatic and if can not return error exception
    protected function inHasManyRelationModelMethodNames() : array|null
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

    abstract protected function inHasManyRelationModel():Model|array;
}
