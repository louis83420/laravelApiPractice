<?php

if (!function_exists('ex_response')) {
    function ex_response()
    {
        return app(\App\Support\ResponseFactory::class);
    }
}
