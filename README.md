# Ultimo MVC Cache
Caching for Ultimo MVC

## Requirements

* PHP 5.3
* Ultimo Cache
* Ultimo MVC

## Usage
	$cache = $application->getPlugin('cache');
        $data = $cache->loadOrUpdate('some_key', function() {
            return expensive_method_to_retrieve_data();
          },
          60, // cache expires in 60 seconds
          5 // extend cache with 5 seconds to prevent cache stampede / dog-pile effect
        )