<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tombenevides\SaidQuotes\SaidQuote;

beforeEach(function () {
    $this->mockHandler = new MockHandler();

    $this->mockedClient = new Client([
        'handler' => $this->mockHandler,
    ]);
});


it('returns a Jane Austen quote', function() {
    $this->mockHandler->append(
        new Response(
            status: 200,
            body: '{"_id":"yHmYezfz4G","tags":["friendship"],"content":"Business, you know, may bring you money, but friendship hardly ever does.","author":"Jane Austen","authorSlug":"jane-austen","length":73,"dateAdded":"2021-02-12","dateModified":"2021-02-12"}'
        )
    );
    
    $quotes = new SaidQuote(author: 'jane austen', client: $this->mockedClient);

    $quote = $quotes->getQuote();

    expect($quote)->toEndWith('Business, you know, may bring you money, but friendship hardly ever does. by Jane Austen');
});

it("returns a message when it doesn't find a matching quote.", function() {
    $this->mockHandler->append(
        new ClientException(
            '{"statusCode":404,"statusMessage":"Could not find any matching quotes"}', 
            new Request('GET', '/'),
            new Response(404)
        )
    );

    $quotes = new SaidQuote(author: 'Sourcegraph Tom', client: $this->mockedClient);

    $quote = $quotes->getQuote();

    expect($quote)
        ->toEqual('Error: Could not find any matching quotes for Sourcegraph Tom');
});

it('returns a message when was not given an author name', function() {
    $quotes = new SaidQuote('');

    $quote = $quotes->getQuote();

    expect($quote)->toContain('Error: An author name is required');
});
