<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UsernameConstraint extends Constraint
{
    public string $message = 'Le nom d\'utilisateur "{{ string }}" est déjà utilisé.';

    public function __construct(?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}