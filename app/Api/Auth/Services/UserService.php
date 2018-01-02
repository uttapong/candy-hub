<?php

namespace GetCandy\Api\Auth\Services;

use GetCandy\Api\Auth\Models\User;
use GetCandy\Api\Scaffold\BaseService;

class UserService extends BaseService
{
    public function __construct()
    {
        $this->model = new User();
    }

    /**
     * Creates a resource from the given data
     *
     * @param  array  $data
     *
     * @return GetCandy\Api\Auth\Models\User
     */
    public function create($data)
    {
        $user = new User();
        $user->password = bcrypt($data['password']);
        $user->name = $data['name'];
        $user->email = $data['email'];

        if (empty($data['language'])) {
            $lang = app('api')->languages()->getDefaultRecord();
        } else {
            $lang = app('api')->languages()->getEnabledByLang($data['language']);
        }

        if (!empty($data['fields'])) {
            $user->fields = $data['fields'];
        }

        $user->save();

        if (!empty($data['customer_groups'])) {
            $groupData = $this->mapCustomerGroupData($data['customer_groups']);
            $user->customerGroups()->sync($groupData);
        }

        $user->language()->associate($lang);

        $user->save();

        return $user;
    }
}
