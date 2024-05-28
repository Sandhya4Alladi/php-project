<?php

class YiiAppMock
{
    private $mocked = false;
    private $app_backup = null;
    private $app_mock = null;

    public function close(): void
    {
        if ($this->mocked) {
            $this->mocked = false;
            Yii::setApplication(null);
            if ($this->app_backup != null) {
                Yii::setApplication($this->app_backup);
                $this->app_backup = null;
                $this->app_mock = null;
            }
        }
    }

    public function mockParams(string $tenant_id, $params){
        $this->mockApp();
        $this->app_mock->shouldReceive('hasComponent')->with('params')->andReturn(true);
        $this->app_mock->shouldReceive('getComponent')->with('params')->andReturn($params);
    }
    public function mockUser(int $tenant_id, string $user_id, $tenant_profile = null, bool $isGuest = false)
    {
        $this->mockApp();

        $user = $this->mockAppComponent('user');
        $user->shouldReceive('getId')->andReturn($user_id);
        $user->shouldReceive('mongoId')->andReturn($user_id . '-mongo');
        $user->id = $user_id;
        $user->shouldReceive('getTenantId')->andReturn($tenant_id);
        $user->shouldReceive('getUserMongo')->andReturn(null);
        $user->shouldReceive('getUserMongoView')->andReturn(null);
        $user->shouldReceive('getTenantProfile')->andReturn($tenant_profile);
        $user->shouldReceive('setUserMongo')->andReturn(null);
        $user->shouldReceive('setUserMongoView')->andReturn(null);
        $user->shouldReceive('setTenantProfile')->andReturn(null);
        $user->shouldReceive('isSetUserMongo')->andReturn(false);
        $user->shouldReceive('isSetUserMongoView')->andReturn(false);
        $user->shouldReceive('isSetTenantProfile')->andReturn(false);
        $user->tenant_id = $tenant_id;
        $user->isGuest = $isGuest;
        $this->app_mock->shouldReceive('getUser')->andReturn($user);
    }

        public function mockRequest(string $url, $isAjaxRequest = true)
    {
        $this->mockApp();

        $request = $this->mockAppComponent('request');
        $request->url = $url;
        $this->app_mock->shouldReceive('createUrl')->andReturn($url);
        $request->isAjaxRequest = $isAjaxRequest;
    }

    public function mockCache()
    {
        $this->mockApp();

        $cache = $this->mockAppComponent('cache');
        $cache->shouldReceive('executeCommand')->andReturn(null);

        return $cache;
    }

    public function mockSession($session_data)
    {
        $this->mockApp();

        $session = $this->mockAppComponent('session', ArrayObject::class);
        foreach ($session_data as $key => $value) {
            $session[$key] = $value;
        }
    }

    public function mockApp()
    {
        if (!$this->mocked) {
            $this->mocked = true;
            $this->app_backup = Yii::app();
            $this->app_mock = Mockery::mock(CApplication::class);

            $mongo = $this->mockAppComponent('mongodb', EMongoDB::class);
            $mongo->connectionString = "mongodb://test";
            $mongo->dbName = "test";

            $commonDb = $this->mockAppComponent('common', EMongoDB::class);
            $commonDb->connectionString = "mongodb://common";
            $commonDb->dbName = "common";

            $coreMessages = $this->mockAppComponent('coreMessages');
            $coreMessages->shouldReceive('translate')->andReturn('');

            $language = $this->mockAppComponent('language');
            $language->shouldReceive('getLanguage')->andReturn('en');

            Yii::setApplication(null);
            Yii::setApplication($this->app_mock);

            $this->app_mock->shouldReceive('end')->andReturn(false);
        }
        return $this->app_mock;
    }

    public function mockAppComponent($component_name, $type = stdClass::class)
    {
        $component = Mockery::namedMock($component_name, $type)->makePartial();
        $this->app_mock->shouldReceive('hasComponent')->with($component_name)->andReturn(true);
        $this->app_mock->shouldReceive('getComponent')->with($component_name)->andReturn($component);
        return $component;
    }
}