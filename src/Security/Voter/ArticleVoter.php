<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Article;
use App\Entity\User;

class ArticleVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const DELETE = 'POST_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof \App\Entity\Article;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $article = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                return $this->canView($article, $user);
                break;
            case self::VIEW:
                // logic to determine if the user can VIEW
                return $this->canEdit($article, $user);
                break;
            case self::DELETE:
                // logic to determine if the user can VIEW
                return $this->canDelete($article, $user);
                break;
        }

        return false;
    }

    private function canView(Article $article, User $user): bool
    {
        if ($this->canEdit($article, $user)) {
            return true;
        }
        return false;
    }

    private function canDelete(Article $article, User $user): bool
    {
        if ($this->canEdit($article, $user)) {
            return true;
        }
        return false;
    }

    private function canEdit(Article $article, User $user): bool
    {
        $users = $article->getUsers();

        foreach ($users as $articleUser) {
            if ($user === $articleUser) {
                return true;
            }
        }

        return false;
    }
}
