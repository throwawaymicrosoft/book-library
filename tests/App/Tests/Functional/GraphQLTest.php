<?php

namespace App\Tests\Functional;

use App\DataFixtures\AppFixtures;
use App\UI\DTO\BookDTO;
use Faker\Factory;
use KunicMarko\GraphQLTest\Bridge\Symfony\TestCase;
use KunicMarko\GraphQLTest\Operation\Mutation;
use KunicMarko\GraphQLTest\Operation\Query;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use ReflectionClass;
use ReflectionProperty;

class GraphQLTest extends TestCase
{
    public static $endpoint = '/graphql/';

    private const DTO = BookDTO::class;

    private const EXCLUDE_FIELDS = [
        'originalFileName',
    ];

    protected function setUp(): void
    {
        self::bootKernel();
        $this->databaseTool = self::$container->get(DatabaseToolCollection::class)->get();

        /**
         * Важно, хедеры без префикса HTTP_ не передаются
         */
        $this->setDefaultHeaders([
            'HTTP_X-API-TOKEN' => 'test',
        ]);

        $this->databaseTool->loadFixtures([
            AppFixtures::class,
        ]);

        $this->faker = Factory::create();
    }

    private function getFields(): array
    {
        $reflect = new ReflectionClass(self::DTO);

        $map = array_map(function (ReflectionProperty $property): string {
            return $property->getName();
        }, $reflect->getProperties(ReflectionProperty::IS_PUBLIC));

        return array_filter($map, function (string $property): bool {
            return !in_array($property, self::EXCLUDE_FIELDS);
        });
    }

    private function countBooks(): int
    {
        $query = $this->query(
            new Query(
                'books',
                [],
                $this->getFields(),
            ),
        );

        return count(
            json_decode($query->getResponse()->getContent(), true)['data']['books']
        );
    }

    public function testBooksMutation(): void
    {
        $preMutation = $this->countBooks();
        $this->assertGreaterThan(0, $preMutation);

        $mutation = $this->mutation(
            new Mutation(
                'createBook',
                [
                    'input' => [
                        'title' => $this->faker->text,
                        'author' => $this->faker->name,
                        'readAt' => $this->faker->date,
                    ]
                ],
                $this->getFields(),
            )
        );

        $postMutation = $this->countBooks();
        $this->assertGreaterThan($preMutation, $postMutation);
    }
}