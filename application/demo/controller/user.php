<?php
namespace frame3\demo;
/**
 *   @name 用户类
 */
class user extends base {
    const ERR_CODE = 3330;
    static $_err_message = [
        self::ERR_CODE + 1 => '用户不存在',
    ];

    // uri : /demo/user  or  /demo/user/index
    // 参数示例 /demo/user?id=1  or /demo/user/index/id/1.html
    public function index() {
        $user = M('user')->where(['id' => intval(I('id'))])->one();
        if (!empty($user)) {
            $this->resp_suc('用户存在！', $user);
        } else {
            $this->resp_err(self::ERR_CODE + 1);
        }
    }
}