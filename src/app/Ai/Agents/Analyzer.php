<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Stringable;

class Analyzer implements Agent, Conversational, HasStructuredOutput, HasTools
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return "Du bist ein System zur Analyse von Bewerbungen.\n
            Extrahiere aus der Stellenausschreibung (job_text) die Anforderungen (requirements).\n
            Extrahiere aus dem Lebenslauf (cv_text) die relevanten Erfahrungen (experiences).\n
            Erstelle anschließend eine Matching-Liste.\n
            \n
            Gib das Ergebnis ausschließlich als JSON zurück:
            {
              \"requirements\": [\"...\"],
              \"experiences\": [\"...\"],
              \"matches\": [{\"requirement\": \"...\", \"experience\": \"...\"}],
              \"gaps\": [\"...\"]
            }";
    }

    /**
     * Get the list of messages comprising the conversation so far.
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [];
    }

    /**
     * Get the agent's structured output schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'requirements' => $schema->array()->items($schema->string())->required(),
            'experiences' => $schema->array()->items($schema->string())->required(),
            'matches' => $schema->array()->items($schema->string())->required(),
            'gaps' => $schema->array()->items($schema->string())->required(),
        ];
    }
}
