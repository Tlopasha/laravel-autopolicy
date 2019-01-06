<?php

namespace musa11971\autopolicy;

use Exception;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use ReflectionClass;

class AutoPolicyProvider extends ServiceProvider
{
    protected $policies = [];

    /** @var string */
    const CACHE_KEY = 'autopolicy_map';

    /** @var int: cache for 24 hours */
    const CACHE_TIME = 1440;

    /**
     * Bootstrap any application services.
     *
     * @throws Exception
     */
    public function boot() {
        $this->policies = $this->getPolicyMap();

        $this->registerPolicies();
    }

    /**
     * Returns an array with the policy map.
     *
     * @return array
     * @throws Exception
     */
    protected function getPolicyMap() {
        // Try to to get the cached map if there is one
        if(Cache::has(self::CACHE_KEY) && !App::isLocal()) {
            return Cache::get(self::CACHE_KEY);
        }

        // Retrieve an array of policies
        $policies = $this->getPolicies();

        // Initialize the policy map
        $map = [];

        foreach($policies as $policy) {
            $data = $this->getPolicyData($policy);

            // Check if the model is not being used as a duplicate
            if(isset($map[$data['model']]))
                throw new Exception('The model "' . $data['model'] . '" is being used for more than one policy."');

            // Assign the policy its spot in the policy map
            $map[$data['model']] = $data['policy'];
        }

        // Cache the map and return it
        if(!App::isLocal())
            Cache::put(self::CACHE_KEY, $map, self::CACHE_TIME);

        return $map;
    }

    /**
     * Retrieves the application's policies.
     *
     * @return array
     */
    protected function getPolicies() {
        $path = app_path('Policies');

        // Return an empty array if there are no policies at all
        if(!file_exists($path))
            return [];

        // Scan for policies
        $policies = scandir($path);

        // Remove hidden items
        foreach ($policies as $key => $policy)
            if(starts_with($policy, '.')) unset($policies[$key]);

        return $policies;
    }

    /**
     * Returns the data necessary to register a policy.
     *
     * @param $policyFile
     * @return mixed
     * @throws Exception
     */
    protected function getPolicyData($policyFile) {
        // Get the class name of the policy
        $policyClassName = chop($policyFile, '.php');

        // Require the policy
        require_once(app_path('Policies/' . $policyFile));

        // Create a reflection class of the policy to retrieve data
        $reflection = new ReflectionClass('\\App\\Policies\\' . $policyClassName);

        // Attempt to get the model
        $model = $reflection->getConstant('MODEL');

        if(!$model) throw new Exception('The policy "' . $policyClassName . '" is not bound to any model.');

        // Return the policy data
        return [
            'model'     => $model,
            'policy'    => $reflection->getNamespaceName() . '\\' . $policyClassName
        ];
    }
}