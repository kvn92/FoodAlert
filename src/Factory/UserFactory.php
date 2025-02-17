<?php
namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\ModelFactory;

/**
 * @extends ModelFactory<User>
 */
final class UserFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'createAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-2 years', 'now')),
            'email' => self::faker()->unique()->safeEmail(),
            'isActive' => self::faker()->boolean(90), // 90% des utilisateurs sont actifs
            'password' => 'password123', // Hashé après instanciation
            'pseudo' => self::faker()->userName(),
            'roles' => ['ROLE_USER'], // ROLE_USER par défaut
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function (User $user): void {
                // Hachage du mot de passe
                $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));
            });
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
