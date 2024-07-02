<?php

declare(strict_types=1);

namespace MainTests\Sylius\Functional;

use App\Factory\BookFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LegacyBookTest extends WebTestCase
{
    use PurgeDatabaseTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testBrowsingBooks(): void
    {
        BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create()
        ;

        BookFactory::new()
            ->withTitle('Carrie')
            ->withAuthorName('Stephen King')
            ->create()
        ;

        $this->client->request('GET', '/admin/legacy/books/');

        self::assertResponseIsSuccessful();
    }

    public function testAddingBookContent(): void
    {
        $this->client->request('GET', '/admin/legacy/books/new');

        self::assertResponseIsSuccessful();
    }

    public function testEditingBookContent(): void
    {
        $book = BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', sprintf('/admin/legacy/books/%s/edit', $book->getId()));

        self::assertResponseIsSuccessful();
    }
}
