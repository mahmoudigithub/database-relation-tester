<?php

namespace Mahmoudigithub\DatabaseRelationTester\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;

trait MorphedToManyRelationTesting
{
    public function test_Morphed_To_Many_Model_Relations()
    {
        // get in relation model|models
        $relationModel = $this->inMorphedToManyRelationModel();

        // use multiple in relation model test when inRelationModel returns array of model
        if (is_array($relationModel)) {
            $this->morphedToManyMultipleTest($relationModel);
        } // use single in relation model test when inRelationModel returns array of model
        else {
            $this->morphedToManySingleTest($relationModel);
        }
    }

    // when inRelationModel returns array of models
    private function morphedToManyMultipleTest(array $inRelationModels)
    {
        foreach ($inRelationModels as $relationModel) {
            $this->morphedToManyExecuteTest($relationModel);
        }
    }

    // when inRelationModel returns single model
    private function morphedToManySingleTest(Model $inRelationModel)
    {
        $this->morphedToManyExecuteTest($inRelationModel);
    }


    private function morphedToManyExecuteTest(Model $inRelationModel)
    {

        // count of new relationModel instance that will create for currentModel
        $count = rand(1, 9);

        // get currentModel
        $currentModel = $this->model();

        $relationMethod = $this->get_able_method_name($currentModel);

        // get currentModel table
        $inRelationModelTableName = $inRelationModel->getTable();

        // create new instance of current model in database by a morphed instance of in relation model
        $currentModelInstance = $currentModel::factory()->for($inRelationModel::factory(), $relationMethod)->create();

        // assert return type of relation method
        $this->assertTrue($currentModelInstance->$relationMethod instanceof $inRelationModel);

        // assert id of card`s person isset
        $this->assertTrue($currentModelInstance->$relationMethod->id != null);

        // assert person exists in database
        $this->assertDatabaseHas($inRelationModelTableName, $currentModelInstance->$relationMethod->withoutRelations()->toArray());
    }

    private function getModelName(model $model): string
    {
        // get model name array
        $modelNameArray = explode('\\', get_class($model));

        // get model name
        $modelName = $modelNameArray[count($modelNameArray) - 1];

        // return
        return $modelName;
    }

    /**
     * Returns morphedTo method name of in relation model
     *
     * For example when current model name that morphed to
     *  is Comment, It will return 'commentable'
     *
     * @param Model $inRelation
     * @return string
     */
    private function get_able_method_name(Model $inRelation)
    {
        $classname = $inRelation::class;

        $namespaceArray = explode('\\', $classname);

        $shortName = $namespaceArray[(count($namespaceArray) - 1)];

        return strtolower($shortName) . 'able';
    }

    abstract protected function model(): Model;

    abstract protected function inMorphedToManyRelationModel(): Model|array;
}
