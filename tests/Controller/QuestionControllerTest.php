<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QuestionControllerTest extends WebTestCase
{
    // верные данные для авторизации
    private array $trueCredentials = ['email' => 'pestova48@mail.ru', 'password' => '123456'];

    // неверные данные для авторизации
    private array $falseCredentials = ['email' => 'false@mail.ru', 'password' => 'false'];

    // Тест для главной и детальной страницы.
    public function testHomePage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('QAservice');
        $this->assertCount(6, $crawler->filter('.col-4'));
        $link = $crawler->selectLink('Перейти к вопросу')->link();
        $client->click($link);
        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('QAservice');
    }

    public function testLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $link = $crawler->selectLink('Авторизация')->link();
        $client->click($link);
        $this->assertResponseStatusCodeSame(200);
        $this->assertPageTitleContains('Авторизация');
        $client->submitForm('Войти', $this->falseCredentials);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-danger', 'Invalid credentials.');
        $client->submitForm('Войти', $this->trueCredentials);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertPageTitleContains('QAservice');
    }

    public function testAdding()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/add/question');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertPageTitleContains('Авторизация');
        $client->submitForm('Войти', $this->trueCredentials);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $question = [
            'question[title]' => '      ',
            'question[question_text]' => '      ',
            'question[category][category_name]' => '      '
        ];
        $client->submitForm('Добавить вопрос', $question);
        $this->assertResponseStatusCodeSame(422);
        $question = [
            'question[title]' => 'Заголовок',
            'question[question_text]' => 'Текст',
            'question[category][category_name]' => 'Категория'
        ];
        $client->submitForm('Добавить вопрос', $question);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertPageTitleContains('QAservice');
    }
}
