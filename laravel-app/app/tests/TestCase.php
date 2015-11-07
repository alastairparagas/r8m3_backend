<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
        protected $domain;
    
	public function createApplication()
	{
            $this->domain = "http://localhost/R8M3/public/api/v1/";
            $unitTesting = true;
            $testEnvironment = 'testing';
            return require __DIR__.'/../../bootstrap/start.php';
	}

}
