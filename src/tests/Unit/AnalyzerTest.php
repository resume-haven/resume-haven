<?php

declare(strict_types=1);

use App\Ai\Agents\Analyzer;
use Illuminate\Contracts\JsonSchema\JsonSchema;

it('liefert die korrekten Instructions', function () {
    $analyzer = new Analyzer();
    $instructions = $analyzer->instructions();
    expect($instructions)->toContain('requirements', 'experiences', 'matches', 'gaps');
});

it('liefert eine leere Message-Liste', function () {
    $analyzer = new Analyzer();
    expect($analyzer->messages())->toBeArray()->toBeEmpty();
});

it('liefert eine leere Tool-Liste', function () {
    $analyzer = new Analyzer();
    expect($analyzer->tools())->toBeArray()->toBeEmpty();
});

it('liefert das korrekte Schema', function () {
    $analyzer = new Analyzer();
    $mockSchema = new class () implements JsonSchema {
        public function array()
        {
            return $this;
        }

        public function items($type)
        {
            return $this;
        }

        public function string()
        {
            return 'string';
        }

        public function required()
        {
            return 'required';
        }

        public function object(\Closure|array $properties = [])
        {
            return $this;
        }

        public function integer()
        {
            return $this;
        }

        public function number()
        {
            return $this;
        }

        public function boolean()
        {
            return $this;
        }
    };
    $schema = $analyzer->schema($mockSchema);
    expect($schema)->toHaveKeys(['requirements', 'experiences', 'matches', 'gaps']);
});
