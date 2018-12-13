<?php

namespace Quicktools\Tests;

/**
 * Laravel + PHPUnit assert that blade files are being loaded.
 *
 * Trait AssertView
 */
trait Viewable
{



    protected $__loadedViews;

    public function captureLoadedViews()
    {
        if (!isset($this->__loadedViews)) {
            $this->__loadedViews = [];
            $this->app['events']->listen('composing:*', function ($view, $data = []) {
                if ($data) {
                    $view = $data[0]; // For Laravel >= 5.4
                }
                $this->__loadedViews[] = $view->getName();
            }
            );
        }
        return $this->__loadedViews;
    }

    /**
     * Assert that all of the given views are loaded.
     *  - expectViewFiles('path.to.view')
     *  - expectViewFiles(['path.to.view', 'path.to.other.view'])
     *  - expectViewFiles('path.to.view', 'path.to.other.view')
     *
     * @param string|array $paths
     */
    public function expectViewFiles($paths)
    {
        $paths = is_array($paths) ? $paths : func_get_args();

        $this->captureLoadedViews();

        $this->beforeApplicationDestroyed(function () use ($paths) {
            $this->assertEmpty(
                $viewsLoaded = array_diff($paths, $this->__loadedViews),
                'These expected view files were not loaded: [' . implode(', ', $viewsLoaded) . ']'
            );
        });
    }

    /**
     * Assert that none of the given views are loaded.
     *  - doesntExpectViewFiles('path.to.view')
     *  - doesntExpectViewFiles(['path.to.view', 'path.to.other.view'])
     *  - doesntExpectViewFiles('path.to.view', 'path.to.other.view')
     *
     * @param string|array $paths
     */
    public function doesntExpectViewFiles($paths)
    {
        $paths = is_array($paths) ? $paths : func_get_args();

        $this->captureLoadedViews();

        $this->beforeApplicationDestroyed(function () use ($paths) {
            $this->assertEmpty(
                $viewsLoaded = array_intersect($this->__loadedViews, $paths),
                'These unexpected view files were loaded: ['.implode(', ', $viewsLoaded).']'
            );
        });
    }

}