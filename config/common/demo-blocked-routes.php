<?php

return [
    //projects
    ['method' => 'POST', 'origin' => 'admin', 'name' => 'projects'],
    ['method' => 'PUT', 'origin' => 'admin', 'name' => 'projects/{id}'],
    ['method' => 'DELETE', 'origin' => 'admin', 'name' => 'projects'],
    ['method' => 'POST', 'name' => 'projects/{id}/publish'],
    ['method' => 'GET', 'name' => 'projects/{id}/download'],

    //templates
    ['method' => 'POST', 'name' => 'templates'],
    ['method' => 'PUT', 'name' => 'templates/{id}'],
    ['method' => 'PUT', 'name' => 'templates/{name}'],
    ['method' => 'DELETE', 'name' => 'templates'],
];