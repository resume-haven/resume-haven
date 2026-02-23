<?php

declare(strict_types=1);


use App\Http\Controllers\AnalyzeController;

it('AnalyzeController besitzt die Methode analyze', function () {
    $controller = new AnalyzeController();
    expect(method_exists($controller, 'analyze'))->toBeTrue();
});
