<?php
namespace frame3\app1;
/**
 *    默认controller
 */
class index extends base {
    // uri: /index/index  or /
    public function index() {
        echo "Welcome to " . config('frame_name') . ".\t" . T();
        return;
    }

    public function view() {
        assign("name", I('name'));
        v();
        return;
    }
}