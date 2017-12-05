<?php
namespace frame3\app1;
/**
 *
 */
class index extends base {
	// uri: /index/index  or /
	public function index() {
		echo "Welcome to " . config('frame_name') . "\t" . T();
	}
}