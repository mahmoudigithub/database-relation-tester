<?php

namespace Mahmoudigithub\DatabaseRelationTester\Traits;

use Illuminate\Database\Eloquent\Model;

trait MorphManyRelationTesting
{
    use AutoFindRelationMethodName;

    public function test_Morph_One_Model_Relations()
    {
        // get in relation model|models
        $relationModel = $this->inMorphManyRelationModel();

        // use multiple in relation model test when inRelationModel returns array of model
        if(is_array($relationModel)){
            $this->MorphManyMultipleTest($relationModel);
        }
        // use single in relation model test when inRelationModel returns array of model
        else{
            $this->MorphManySingleTest($relationModel);
        }
    }

    // when inRelationModel returns array of models
    private function morphManyMultipleTest(array $inRelationModels)
    {
        foreach($inRelationModels as $relationModel)
        {
            $relationMethod = $this->extract_relation_method_name($this->model() , $relationModel);
            $this->morphManyExecuteTest($relationModel , $relationMethod);
        }
    }

    // when inRelationModel returns single model
    private function morphManySingleTest(Model $inRelationModel)
    {
        $relationMethod = $this->extract_relation_method_name($this->model() , $inRelationModel);
        $this->morphManyExecuteTest($inRelationModel , $relationMethod);
    }



    private function morphManyExecuteTest(Model $InRelationModel ,string $relationMethod)
    {
        // count of new inRelationModel
        $count = rand(1 , 9);

        // get currentModel
        $currentModel = $this->model();

        // get currentModel table
        $inRelationModelTableName = $InRelationModel->getTable();

         // create new instance of current model in database by a belongs instance of in relation model
        $currentModelInstance = $currentModel::factory()->has($InRelationModel::factory($count) , $relationMethod)->create();

        // assert relation method return a in relation model instance
        $this->assertTrue($currentModelInstance->$relationMethod[0] instanceof $InRelationModel);

        // assert relation address id is set
        $this->assertTrue(isset($currentModelInstance->$relationMethod[0]->id));

        // assert user address exists in database
        $this->assertDatabaseHas($inRelationModelTableName , $currentModelInstance->$relationMethod[0]->toArray());
    }

    abstract protected function model():Model;

    // this method can be over written for define relation method name else trait try to find it automatic and if can not return error exception
    protected function inMorphManyRelationModelMethodNames() : array|null
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


    abstract protected function inMorphManyRelationModel():Model|array;
}
