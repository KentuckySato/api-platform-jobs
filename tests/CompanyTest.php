<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Company;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class CompanyTest extends ApiTestCase
{
    // This trait provided by AliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

    const URL = '/api/companies';

    public function testGetCompanies(): void
    {
        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClient()->request('GET', self::URL);

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

    public function testCreateCompany(): void
    {
        $response = static::createClient()->request('POST', self::URL, ['json' => [
            "name" => "Facebook Test Company",
            "description" => "My Company Test",
            "phone" => "+33987988754",
            "website" => "https://facebook.fr",
            "siren" => "123",
            "siret" => "456"
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Company',
            '@type' => 'Company',
            "name" => "Facebook Test Company",
            "description" => "My Company Test",
            "phone" => "+33987988754",
            "website" => "https://facebook.fr",
            "siren" => "123",
            "siret" => "456",
            "recruiters" => []
        ]);
        $this->assertMatchesRegularExpression('~^/api/companies/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Company::class);
    }

    public function testUpdateCompany(): void
    {
        $client = static::createClient();
        // findIriBy allows to retrieve the IRI of an item by searching for some of its properties.
        // ISBN 9786644879585 has been generated by Alice when loading test fixtures.
        // Because Alice use a seeded pseudo-random number generator, we're sure that this ISBN will always be generated.
        $iri = $this->findIriBy(Company::class, ['id' => '1']);

        $client->request('PUT', $iri, ['json' => [
            'name' => 'Facebook Test Company Updated',
            "description" => "My Company Updated",
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'Facebook Test Company Updated',
            "description" => "My Company Updated",
            "updatedAt" => (new \DateTime())->format('Y-m-d\TH:i:sP'),
        ]);
    }
}
