<?php

use Illuminate\Database\Seeder;

class MethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ld_methods')->insert([
	        [
	            'id' => 1, 
	            'name' => '直播', 
	        ], 
	        [
	            'id' => 2, 
	            'name' => '录播', 
	        ],  
	        [
	            'id' => 3, 
	            'name' => '面授', 
	        ], 
	        [
	            'id' => 4, 
	            'name' => '其他', 
	        ],

	    ]);
    }
}
