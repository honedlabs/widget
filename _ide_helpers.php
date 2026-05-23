<?php

declare(strict_types=1);

namespace Illuminate\Contracts\Foundation {

    /**
     * @method bool widgetsAreCached() Determine if the widgets are cached.
     * @method string getCachedWidgetsPath() Get the path to the widgets cache file.
     */
    interface Application {}
}

namespace Illuminate\Support\Facades {
    /**
     * @method static bool widgetsAreCached() Determine if the widgets are cached.
     * @method static string getCachedWidgetsPath() Get the path to the widgets cache file.
     */
    class App {}
}
