<?php

namespace App\Tests\Functional;

use App\DataFixtures\UserFixture;
use Faker\Factory;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControllerBookTest extends WebTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();

        $this->databaseTool = self::$container
            ->get(DatabaseToolCollection::class)
            ->get();

        $this->databaseTool->loadFixtures([
            UserFixture::class,
        ]);

        $this->faker = Factory::create('ru_RU');
    }

    /**
     * При попытке создания книги неавторизованному пользователю выдаётся
     * редирект на форму авторизации
     *
     * @return void
     */
    public function testNewWithoutAuthentication(): void
    {
        $client = static::createClient();

        $client->request(
            Request::METHOD_GET,
            self::$container->get('router')->generate('book_new'),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testCreateBook(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $client->clickLink('Войти');

        $client->submitForm('_submit', [
            '_username' => UserFixture::USERNAME,
            '_password' => UserFixture::PASSWORD,
        ]);
        $this->assertResponseIsSuccessful();

        $client->clickLink('Добавить книгу');
        $this->assertResponseIsSuccessful();

        $client->submitForm('Сохранить', [
            'book[title]' => $this->faker->text(64),
            'book[author]' => $this->faker->name(),
            'book[read_at]' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'book[cover]' => new UploadedFile(
                dirname(__DIR__) . '/test.png',
                $this->faker->text(16)
            ),
            'book[file]' => new UploadedFile(
                dirname(__DIR__) . '/test.pdf',
                $this->faker->text(16)
            ),
            'book[allow_download]' => true,
        ]);
        $this->assertResponseIsSuccessful();
    }
}