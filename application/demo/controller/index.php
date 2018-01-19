<?php
namespace frame3\demo;
/**
 *    @name 首页
 */
class index extends base {
    // uri: /index/index  or /
    public function index() {
        echo "Welcome to " . config('frame_name') . ".\t" . T();
        return;
    }

    public function foo() {
        // assign("name", I('name'));
        V('index/foo/one/tpl');
        return;
    }
}