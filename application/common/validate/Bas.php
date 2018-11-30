<?php
namespace app\common\validate;
use think\Validate;
class Bas extends Validate {
    protected  $rule = [
				'name' => 'require|max:25',
				'email' => 'require|email',
				'city_id' => 'require',
				'bank_info' => 'require',
				'bank_name' => 'require',
				'bank_user' => 'require',
				'logo' => 'require',
				'licence_logo' => 'require',
				'se_city_id' => 'require',
				'faren' => 'require',
				'faren_tel' => 'require',
				'tel' => 'require',
				'category_id' => 'require',
				'se_category_id' => 'require',
				'contact' => 'require',
				'address' => 'require',
				'username' => 'require',
				'password' => 'require',
    ];

    /**场景设置**/
    protected  $scene = [
                'add' => ['name'],
       
    ];
}