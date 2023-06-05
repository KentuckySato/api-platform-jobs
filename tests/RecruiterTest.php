<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Recruiter;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class RecruiterTest extends ApiTestCase
{
    // This trait provided by AliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

    const URL = '/api/recruiters';
    const MODEL_CLASS = 'Recruiter';
    const MODELS = 'recruiters';
    const MODEL = 'recruiter';

    public function testGetRecruiters(): void
    {
        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClient()->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => '/api/contexts/' . self::MODEL_CLASS,
            '@id' => '/api/' . self::MODELS,
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 5,
        ]);

        // Because test fixtures are automatically loaded between each test, you can assert on them
        // Be sure that total items is the same as the number of items loaded by Alice
        $this->assertCount(5, $response->toArray()['hydra:member']);

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
        $this->assertMatchesResourceCollectionJsonSchema(Recruiter::class);
    }

    public function testGetOneRecruiter(): void
    {
        $response = static::createClient()->request('GET', self::URL . '/1');

        $this->assertMatchesRegularExpression('~^/api/' . self::MODELS . '/\d+$~', $response->toArray()['@id']);
        $this->assertResponseIsSuccessful(self::MODEL . ' retrieve successful');
    }

    public function testCreateRecruiter(): void
    {
        $response = static::createClient()->request('POST', self::URL, ['json' => [
            "company" => "/api/companies/1",
            "firstname" => "test",
            "lastname" => "UNIT",
            "email" => "hello@testUnit.com",
            "phone" => "+33 999999999",
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/' . self::MODEL_CLASS,
            '@type' => self::MODEL_CLASS,
            "firstname" => "test",
            "lastname" => "UNIT",
            "email" => "hello@testUnit.com",
            "phone" => "+33 999999999",
        ]);
        $this->assertMatchesRegularExpression('~^/api/' . self::MODELS . '/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Recruiter::class);
    }

    public function testUpdateCompany(): void
    {
        $client = static::createClient();
        // findIriBy allows to retrieve the IRI of an item by searching for some of its properties.
        // ISBN 9786644879585 has been generated by Alice when loading test fixtures.
        // Because Alice use a seeded pseudo-random number generator, we're sure that this ISBN will always be generated.
        $iri = $this->findIriBy(Recruiter::class, ['id' => '1']);

        $client->request('PUT', $iri, ['json' => [
            "firstname" => "tested",
            "lastname" => "UNITED",
            "email" => "hello@testedUnited.com",
            "phone" => "+33 888888888",
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            "firstname" => "tested",
            "lastname" => "UNITED",
            "email" => "hello@testedUnited.com",
            "phone" => "+33 888888888",
            "updatedAt" => (new \DateTime())->format('Y-m-d\TH:i:sP'),
        ]);
    }

    public function testDeleteCompany(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(Recruiter::class, ['id' => '1']);

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            // Through the container, you can access all your services from the tests, including the ORM, the mailer, remote API clients...
            static::getContainer()->get('doctrine')->getRepository(Recruiter::class)->findOneBy(['id' => '1'])
        );
    }
}
