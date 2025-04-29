<?php

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UsernameConstraintValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {

        if (!$constraint instanceof UsernameConstraint) {
            throw new UnexpectedTypeException($constraint, UsernameConstraint::class);
        }

// On vérifie que le champ n'est ni nul ni vide
// pour ne pas utiliser en + les attributs NotNull et NotBalnck
        if (null === $value || '' === $value) {
            return;
        }

// Logique métier de la méthode
        $user = $this->userRepository->findByUsername($value);

        if ($user) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}