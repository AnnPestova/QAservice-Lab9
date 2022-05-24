<?php

namespace App\DataFixtures;

use Symfony\Component\Uid\Uuid;
use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\QuestionCategory;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $roles = [
            'ROLE_USER',
            'ROLE_ADMIN',
        ];
        // пользователь
        $user = new User();
        $password = $this->hasher->hashPassword($user, '123456');
        $user->setName('Миронова Злата')
            ->setApiToken(Uuid::v1()->toRfc4122())
            ->setEmail('mironova48@mail.ru')
            ->setPassword($password)
            ->setRoles($roles);
        $manager->persist($user);

        $user = new User();
        $password = $this->hasher->hashPassword($user, '123456');
        $user->setName('Пестова Анна')
            ->setApiToken(Uuid::v1()->toRfc4122())
            ->setEmail('pestova48@mail.ru')
            ->setPassword($password)
            ->setRoles($roles);
        $manager->persist($user);

        // категория вопроса
        $questionCategory1 = new QuestionCategory();
        $questionCategory1->setCategoryName('Тестовая категория');
        $manager->persist($questionCategory1);

        for ($i = 1; $i < 51; $i++) {
            $question = new Question();
            $question->setUser($user)
                ->setCategory($questionCategory1)
                ->setTitle('Заголовок вопроса №' . $i)
                ->setQuestionText('Текст вопроса №' . $i)
                ->setQuestionDate(new \DateTime('now + ' . $i . ' hours'))
                ->setModerationStatus(true);
            $manager->persist($question);

            $question1 = new Question();
            $question1->setUser($user)
                ->setCategory($questionCategory1)
                ->setTitle('Заголовок вопроса №' . $i)
                ->setQuestionText('Текст вопроса №' . $i)
                ->setQuestionDate(new \DateTime('now + ' . $i . ' hours'));
            $manager->persist($question1);

            $answer = new Answer();
            $answer->setUser($user)
                ->setQuestion($question)
                ->setAnswerText('Ответ на вопрос №' . $i)
                ->setAnswerDate(new \DateTime('now + ' . $i . ' hours'))
                ->setAnswerCorrectness(0)
                ->setModerationStatus(true);
            $manager->persist($answer);

            $answer1 = new Answer();
            $answer1->setUser($user)
                ->setQuestion($question1)
                ->setAnswerText('Ответ на вопрос №' . $i)
                ->setAnswerDate(new \DateTime('now + ' . $i . ' hours'))
                ->setAnswerCorrectness(0);
            $manager->persist($answer1);
        }


        $manager->flush();
    }
}
