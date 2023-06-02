<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Company;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class CompanyTest extends ApiTestCase
{
    // This trait provided by AliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

    public function testGetCollection(): void
    {
        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClient()->request('GET', '/api/companies');

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => '/api/contexts/Company',
            '@id' => '/api/companies',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 10,
        ]);

        // Because test fixtures are automatically loaded between each test, you can assert on them
        // Be sure that total items is the same as the number of items loaded by Alice
        $this->assertCount(10, $response->toArray()['hydra:member']);

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
        $this->assertMatchesResourceCollectionJsonSchema(Company::class);
    }
}
