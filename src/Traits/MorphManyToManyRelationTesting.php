<?php

namespace Mahmoudigithub\DatabaseRelationTester\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;

trait MorphManyToManyRelationTesting
{
    use AutoFindRelationMethodName;

    public function test_Morph_Many_To_Many_Model_Relations()
    {
        // get in relation model|models
        $relationModel = $this->inMorphManyToManyRelationModel();

        // use multiple in relation model test when inRelationModel returns array of model
        if(is_array($relationModel)){
            $this->morphManyToManyMultipleTest($relationModel);
        }
        // use single in relation model test when inRelationModel returns array of model
        else{
            $this->morphManyToManySingleTest($relationModel);
        }
    }

    // when inRelationModel returns array of models
    private function morphManyToManyMultipleTest(array $inRelationModels)
    {
        foreach($inRelationModels as $relationModel)
        {
            $relationMethod = $this->extract_relation_method_name($this->model() , $relationModel);
            $this->morphManyToManyExecuteTest($relationModel , $relationMethod);
        }
    }

    // when inRelationModel returns single model
    private function morphManyToManySingleTest(Model $inRelationModel)
    {
        $relationMethod = $this->extract_relation_method_name($this->model() , $inRelationModel);
        $this->morphManyToManyExecuteTest($inRelationModel , $relationMethod);
    }



    private function morphManyToManyExecuteTest(Model $inRelationModel ,string $relationMethod)
    {
        // count of new relationModel instance that will create for currentModel
        $count = rand(1 , 9);

        // get currentModel
        $currentModel = $this->model();

        // get currentModel table
        $inRelationModelTableName = $inRelationModel->getTable();

         // create new instance of current model in database by a morphMany instance of in relation model
        $currentModelInstance = $currentModel::factory()->hasAttached($inRelationModel::factory($count))->create();

        // assert return type of relation method
        $this->assertTrue($currentModelInstance->$relationMethod[0] instanceof $inRelationModel);

        // assert id of card`s person isset
        $this->assertTrue($currentModelInstance->$relationMethod[0]->id != null);

        // assert person exists in database
        $this->assertDatabaseHas($inRelationModelTableName , $currentModelInstance->$relationMethod[0]->withoutRelations()->toArray());

        // assert pivot table has attach in database
        $this->assertDatabaseHas('posterables' , [
            $this->getModelName($inRelationModel) .'_id' => $currentModelInstance->$relationMethod[0]->id ,
            $this->getModelName($inRelationModel) .'able_id' => $currentModelInstance->id ,
            $this->getModelName($inRelationModel) .'able_type' => ($currentModelInstance::class)
        ]);
    }

    private function getModelName(model $model):string
    {
        // get model name array
        $modelNameArray = explode('\\' , get_class($model));

        // get model name
        $modelName = $modelNameArray[count($modelNameArray) - 1];

        // return
        return $modelName;
    }

    abstract protected function model():Model;

    // this method can be over written for define relation method name else trait try to find it automatic and if can not return error exception
    protected function inMorphManyToManyRelationModelMethodNames() : array|null
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


    abstract protected function inMorphManyToManyRelationModel():Model|array;
}
