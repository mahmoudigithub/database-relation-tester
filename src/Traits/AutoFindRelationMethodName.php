<?php

namespace Mahmoudigithub\DatabaseRelationTester\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;

trait AutoFindRelationMethodName
{

        // this method will to try get relation method name from defined array by programmer whitin abstract in.....ModelMethodNames function (for example inBelongsToRelationModelMethodNames of BelongsToRelationTesting trait)
        private function get_predefined_relation_methods_list_function_name(Model $relationModel)
        {
            // relation trait name for exmpale BelongsToRelationTesting name that will be extract by debug_backtrace and set to it
            $relationTraitName = null;

            // current trait name
            $current_trait_name = __TRAIT__;

            // try to find
            foreach(debug_backtrace() as $caller)
            {
                // check it method caller whitin this trait . if is , will continue to next itrate
                if(strpos(strtolower(str_replace(DIRECTORY_SEPARATOR , '\\' , $caller['file'])) , strtolower($current_trait_name)))
                {
                    continue;
                }

                // extract trait|class name from file path
                $relationTraitFileNameArray =  explode(DIRECTORY_SEPARATOR , $caller['file']);
                $relationTraitFileName = $relationTraitFileNameArray[count($relationTraitFileNameArray) - 1];
                $relationTraitName = substr($relationTraitFileName , 0 , strpos($relationTraitFileName , 'Testing.php'));
                break;
            }

            // predefined relation method list funciton name
            $predefinedRelationMethodListFunctionName = 'in'. $relationTraitName . 'ModelMethodNames';

            // return
            return $predefinedRelationMethodListFunctionName;
        }

        // this method will extract relation method name of relation class or inHasOneRelationModelMethodNames method array
        private function extract_relation_method_name(Model $currentModel , Model $relationModel)
        {
            // get relation method name from predefined abstract function
            $relation_method_list_function_name = $this->get_predefined_relation_methods_list_function_name($relationModel);

            // if inHasOneRelationModelMethodNames overwritten , it tries to extract relation method name form inHasOneRelationModelMethodNames method return array
            if($this->$relation_method_list_function_name())
            {
                if($relation_method_name = $this->auto_extract_relation_method_name_from_relation_methods_array($relationModel , $relation_method_list_function_name)) {
                    return $relation_method_name;
                }
            }
            // try to extract realtion method name from relation model class name and current model methods list . if can not , gonna to throw exception
            return $this->auto_extract_relation_method_name_from_models($currentModel , $relationModel);
        }

        // it method try to exract relation method name from inHasOneRelationModelMethodName return
        private function auto_extract_relation_method_name_from_relation_methods_array(Model $relationModel  , $relationMethodListFunctionName):string|false
        {
            // array search by relation model value
            $relation_method_name = array_search($relationModel , $this->$relationMethodListFunctionName());

            // check relation_method_name find or not . if not gonna to return error exception
            if($relation_method_name)
                return $relation_method_name;
            else {
                return false;
            }
        }

        // this method try to extract relation method name by regard relation model class name and current model method list
        private function auto_extract_relation_method_name_from_models(Model $currentModel , Model $relationModel)
        {
            // get class name array
            $in_relation_model_class_name_array = explode('\\' , get_class($relationModel));

            // get class name
            $in_relation_model_class_name = $in_relation_model_class_name_array[count($in_relation_model_class_name_array) - 1];

            // relation method is strtolower of in relation model class name
            $relation_method_name = strtolower($in_relation_model_class_name);

            // get current model methods list
            $current_model_method_list = get_class_methods($currentModel);

            // check relation method exists . if exists test gonna to execute
            foreach($current_model_method_list as $methodName)
            {
                if(str_contains($methodName , $relation_method_name))
                {
                    return $methodName;
                }
            }
            // if(in_array($relation_method_name , $current_model_method_list)) {
            //     return $relation_method_name;
            // } else {
                throw new Exception(
                    'Relation method by (' . $relation_method_name . ') name not found in ' . get_class($currentModel) . '. If you want use of custom name for relation method , you can overwrite inHasOneRelationModelMethodName method of this trait .'
                    , E_ERROR);
            // }
        }

}
