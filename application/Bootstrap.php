<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initMysql() {

	    $this->bootstrap('db');

	    switch (APPLICATION_ENV) {

	        case 'development' :
	            // this allows you to profile your queries through the firebug console 
	            $profiler = new Zend_Db_Profiler_Firebug('System Queries');
	            $profiler->setEnabled(true);
	            $this->getPluginResource('db')->getDbAdapter()->setProfiler($profiler);
	            Zend_Registry::set("db",Zend_Db_Table::getDefaultAdapter());
	            break;

	        case 'production' :
	            // if you use meta caching in production, which you should :)
	            // Zend_Db_Table_Abstract::setDefaultMetadataCache($this->_cache);
	            break;
	    }
	}

	protected function _initLogger() {
		$writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . "/appLogs/".date("Y-m-d"));
	    $logger = new Zend_Log($writer);
	    Zend_Registry::set('logger', $logger);
	}

}

