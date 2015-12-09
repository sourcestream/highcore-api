<?php
namespace Highcore\Models;

use Log;
use Stringy\Stringy;

class ComponentCollection extends Collection
{
    /**
     * @inheritdoc
     * @param string $class Cast data arrays to models of the specified class
     * @param string $key_by Key collection using a getter with the specified key
     *
     * @return static
     */
    public static function make($items = null, $class = null, $key_by = null){
        return parent::make($items, $class, $key_by)->sortByDesc(function($i, $k){
            //we always sort components by their key length to ensure
            // longest-key is taken first for resources distribution
            return strlen($k);
        });
    }

    public function distributeResources($resources, $componentPrefixedKey = 'LogicalResourceId')
    {
        Log::debug(__METHOD__.' start');
        $groupedResourceCollection = [];
        foreach($this->items as $componentKey => $component) {
            foreach($resources as $resourceKey => $resource){
                if(Stringy::create($resource[$componentPrefixedKey])->startsWith(studly_case($componentKey), false)){
                    $groupedResourceCollection[$componentKey][] = $resource;
                    unset($resources[$resourceKey]);
                }
                if($resource[$componentPrefixedKey] == data_get($resource, 'StackName') or $resource[$componentPrefixedKey] == ''){
                    $groupedResourceCollection['_stack'][] = $resource;
                    unset($resources[$resourceKey]);
                }
            }
        }

        if(count($resources) > 0) {
            $groupedResourceCollection['_ungrouped'] = $resources;
        }

        $groupedResourceCollectionKeys = array_keys($groupedResourceCollection);

        $retval = Collection::make($groupedResourceCollection)
            ->map(function ($resources) {
                return Collection::make($resources);
            })
            ->replaceKeys(function ($k) use ($groupedResourceCollectionKeys) {
                return $groupedResourceCollectionKeys[$k];
            });

        Log::debug(__METHOD__.' end');
        return $retval;

    }
}